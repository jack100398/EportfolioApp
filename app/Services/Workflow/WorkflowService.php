<?php

namespace App\Services\Workflow;

use App\Models\Course\CourseAssessment;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use App\Services\Interfaces\IWorkflowService;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\Enum\ProcessStateEnum;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WorkflowService implements IWorkflowService
{
    public function getById(int $id): Workflow
    {
        return Workflow::findOrFail($id);
    }

    public function getByDataId(int $dataId, int $workflowTypeEnum): Collection
    {
        return Workflow::where([['data_id', $dataId], ['type', $workflowTypeEnum]])->get();
    }

    public function deleteById(int $id): bool
    {
        return Workflow::findOrFail($id)->delete() === true;
    }

    public function getByIdWithProcess(int $dataId, int $workflowTypeEnum): Collection
    {
        return Workflow::with('processes')->where([['data_id', $dataId], ['type', $workflowTypeEnum]])->get();
    }

    public function getByWorkflowTypePagination(array $conditions): LengthAwarePaginator
    {
        $workflow = $this->getWorkflowProcessState($conditions);

        return $this->getExtraCondition($workflow, $conditions)->paginate($conditions['per_page']);
    }

    public function getByIdWithForm(int $id): ?Workflow
    {
        return Workflow::where('id', $id)->with('form')->first();
    }

    public function getErrorWorkflowPagination(array $conditions): LengthAwarePaginator
    {
        $errorWorkflow = $this->getWorkflowErrorStatus($conditions);

        return $this->getExtraCondition($errorWorkflow, $conditions)->paginate($conditions['per_page']);
    }

    public function getWorkflowLateUser(int $id): ?Workflow
    {
        return Workflow::where([['end_at', '<', date('Y-m-d')], ['id', $id]])
            ->with('processes', function ($query) {
                return $query->with('user')->whereIn(
                    'state',
                    [ProcessStateEnum::NO_START, ProcessStateEnum::STARTED]
                );
            })->first();
    }

    public function getThresholdFormWorkflowMany(array $thresholdFormIds): Collection
    {
        return Workflow::where('type', WorkflowTypeEnum::THRESHOLD)
            ->with('thresholdForm')
            ->whereIn('data_id', $thresholdFormIds)
            ->get();
    }

    public function getByIds(array $ids): Collection
    {
        return Workflow::whereIn('id', $ids)->with('workflowEvaluatee')->get();
    }

    public function updateReturnWorkflow(int $id): Workflow
    {
        $workflow = $this->getById($id);
        $workflow->is_return = true;
        $workflow->update();

        return $workflow;
    }

    public function replaceReturnWorkflow(Workflow $returnWorkflow): Workflow
    {
        $newWorkflow = new Workflow();
        $newWorkflow->evaluatee = $returnWorkflow->evaluatee;
        $newWorkflow->title = $returnWorkflow->title;
        $newWorkflow->training_program_id = $returnWorkflow->training_program_id;
        $newWorkflow->form_id = $returnWorkflow->form_id;
        $newWorkflow->unit_id = $returnWorkflow->unit_id;
        $newWorkflow->type = $returnWorkflow->type;
        $newWorkflow->data_id = $returnWorkflow->data_id;
        $newWorkflow->start_at = $returnWorkflow->start_at;
        $newWorkflow->end_at = $returnWorkflow->end_at;
        $newWorkflow->create_by = $returnWorkflow->create_by;
        $newWorkflow->save();

        return $newWorkflow;
    }

    public function mapSendWorkflowList(array $workflows): Collection
    {
        return collect($workflows)->map(function ($workflow) {
            $workflowLateUser = $this->getWorkflowLateUser($workflow->id);

            return [
                'id' => $workflow->id,
                'trainingProgramName' => $workflow->trainingProgram['name'],
                'unitName' => $workflow->unit['name'],
                'formName' => is_null($workflow->form) ? null : $workflow->form->name,
                'course_id' => $workflow->type === WorkflowTypeEnum::COURSE ?
                    $this->getCourseId($workflow->data_id) : null,
                'title' => $workflow->title,
                'start_at' => $workflow->start_at,
                'end_at' => $workflow->end_at,
                'evaluatee' => is_null($workflow->workflowEvaluatee) ? null : $workflow->workflowEvaluatee->name,
                'lateUser' => ! is_null($workflowLateUser) && ! is_null($workflowLateUser->processes->first())
                && ! is_null($workflowLateUser->processes->first()->user)
                    ? $this->showLateUserName($workflowLateUser) : null,
            ];
        });
    }

    public function mapErrorWorkflowList(array $workflows): Collection
    {
        return collect($workflows)->map(function ($workflow) {
            return [
                'id' => $workflow->id,
                'trainingProgramName' => $workflow->trainingProgram['name'],
                'unitName' => $workflow->unit['name'],
                'formName' => $workflow->form->name,
                'course_id' => $workflow->type === WorkflowTypeEnum::COURSE ?
                    $this->getCourseId($workflow->data_id) : null,
                'title' => $workflow->title,
                'start_at' => $workflow->start_at,
                'end_at' => $workflow->end_at,
                'evaluatee' => $workflow->workflowEvaluatee->name,
                'error_status' => $workflow->error_status === ProcessErrorStatusEnum::NO_SETTING_ROLE
                    ? '沒有設定簽核流程角色' : '簽核流程人員已不存在',
            ];
        });
    }

    private function showLateUserName(?Workflow $workflowLateUser): string
    {
        return ! is_null($workflowLateUser) && ! is_null($workflowLateUser->processes->first())
        && ! is_null($workflowLateUser->processes->first()->user)
        ?
            $this->checkIsAnonymous($workflowLateUser->processes->first())
        : '';
    }

    private function checkIsAnonymous(Process $process): string
    {
        $userName = is_null($process->user) ? '' : $process->user->name;

        return $process->type === ProcessTypeEnum::ANONYMOUS ? '匿名填寫' :
        $userName;
    }

    private function getCourseId(int $id): int
    {
        return CourseAssessment::findOrFail($id)->course_id;
    }

    private function getExtraCondition(Builder $workflow, array $conditions): Builder
    {
        if (isset($conditions['form_ids'])) {
            $workflow->whereIn('form_id', $conditions['form_ids']);
        }

        if (isset($conditions['start_at']) && isset($conditions['end_at'])) {
            $workflow->where([['start_at', '>=', $conditions['start_at']], ['end_at', '<=', $conditions['end_at']]]);
        }

        return $workflow;
    }

    private function getWorkflowProcessState(array $conditions): Builder
    {
        $builder = Workflow::whereIn('type', $conditions['types'])
            ->whereIn('unit_id', $conditions['unit_ids']);
        if (isset($conditions['training_program_ids'])) {
            $builder = $builder->whereIn('training_program_id', $conditions['training_program_ids']);
        }

        $builder = $builder->where('is_return', false)
            ->with('trainingProgram')
            ->with('form')
            ->whereRelation('form', 'is_enabled', '=', true)
            ->whereRelation('form', 'reviewed', '=', true)
            ->with('unit')
            ->with('workflowEvaluatee')
            ->with('processes', function ($query) {
                return $query->with('user');
            });

        return $this->getSwitchWorkflowState($builder, $conditions['state']);
    }

    /**
     * 判斷簽核狀態.
     */
    private function getSwitchWorkflowState(Builder $query, string $state): Builder
    {
        switch ($state) {
            case 'all':
                return $query;
            case 'end':
                return $query->whereNotIn(
                    'id',
                    Process::whereIn('state', [ProcessStateEnum::STARTED, ProcessStateEnum::NO_START])
                        ->whereNull('next_process_id')->groupBy()->select('workflow_id')
                );
            case 'progress':
            default:
                return $query->whereIn(
                    'id',
                    Process::whereIn('state', [ProcessStateEnum::STARTED, ProcessStateEnum::NO_START])
                        ->whereNull('next_process_id')->groupBy()->select('workflow_id')
                );
        }
    }

    private function getWorkflowErrorStatus(array $conditions): Builder
    {
        return Workflow::whereIn('type', $conditions['types'])
            ->whereIn('training_program_id', $conditions['training_program_ids'])
            ->whereIn('unit_id', $conditions['unit_ids'])
            ->with('form')
            ->with('trainingProgram')
            ->with('workflowEvaluatee')
            ->whereRelation('form', 'is_enabled', '=', true)
            ->whereRelation('form', 'reviewed', '=', true)
            ->whereIn('id', Process::whereIn(
                'error_status',
                $conditions['error_status']
            )->orderBy('next_process_id')->groupBy()->select('workflow_id'))
            ->with('processes');
    }
}
