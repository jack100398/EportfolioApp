<?php

namespace Database\Transfers;

use App\Models\Auth\User;
use App\Models\Course\CourseAssessment;
use App\Models\Workflow\IgnoreThresholdForm;
use App\Models\Workflow\ManualForm;
use App\Models\Workflow\Process;
use App\Models\Workflow\ThresholdForm;
use App\Models\Workflow\Workflow;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use DB;
use Illuminate\Support\Collection;

/**
 * 轉換 門檻、人工發送、課程表單、簽核流程.
 */
class WorkflowTransfer
{
    public function transfer(): void
    {
        $this->transferThresholdForm();
        $this->transferIgnoreThresholdForm();
        $this->transferWorkflow();
    }

    private function transferIgnoreThresholdForm()
    {
        $index = 0;

        $start = microtime(true);

        while (true) {
            $ignoreThresholdForms = $this->getIgnoreThresholdForm($index++);

            if ($ignoreThresholdForms->count() < 1) {
                break;
            }

            foreach ($ignoreThresholdForms as $ignoreThresholdForm) {
                $this->storeIgnoreThresholdForm($ignoreThresholdForm);
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        echo $time_elapsed_secs;
    }

    private function transferWorkflow()
    {
        $index = 0;

        $start = microtime(true);

        while (true) {
            $workflows = $this->getWorkflow($index++);

            if ($workflows->count() < 1) {
                break;
            }

            foreach ($workflows as $workflow) {
                $this->checkTransferWorkflowForm($workflow);
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        echo $time_elapsed_secs;
    }

    private function checkTransferWorkflowForm(object $workflow)
    {
        $type = $this->checkWorkflowType($workflow);
        switch ($type) {
            case WorkflowTypeEnum::COURSE:
                $this->storeCourseForm($workflow);
                break;

            case WorkflowTypeEnum::THRESHOLD:
                $this->storeWorkflow($workflow, $workflow->threshold_id);
                break;
            default:
                $data = $this->storeManualForm($workflow);
                if (! is_null($data)) {
                    $this->storeWorkflow($workflow, $data->id);
                }
                break;
        }
    }

    private function transferThresholdForm()
    {
        $index = 0;

        $start = microtime(true);

        while (true) {
            $thresholdForms = $this->getThresholdForm($index++);

            if ($thresholdForms->count() < 1) {
                break;
            }

            foreach ($thresholdForms as $thresholdForm) {
                $this->storeThresholdForm($thresholdForm);
            }
        }

        $time_elapsed_secs = microtime(true) - $start;
        echo $time_elapsed_secs;
    }

    private function checkWorkflowType(object $workflow): int
    {
        if ($workflow->is_course === 1) {
            return WorkflowTypeEnum::COURSE;
        }
        if ($workflow->threshold_id !== null) {
            return WorkflowTypeEnum::THRESHOLD;
        }

        return WorkflowTypeEnum::MANUAL;
    }

    //TODO:課程表單轉換
    private function storeCourseForm(object $workflow)
    {
        if ($workflow->ai_id === null) {
            //未建立門檻表單 需要補發
            return;
        }
        $courseAssessment = $this->getCourseAssessment($workflow);

        if (! is_null($courseAssessment)) {
            $this->storeWorkflow($workflow, $courseAssessment->id);
        }
    }

    private function getCourseAssessment(object $workflow): ?CourseAssessment
    {
        $courseAssessment = $this->getCourseAssessmentId($workflow->id);
        $assessmentType = $this->getAssessmentType($courseAssessment->type_id, $workflow->group_id, $courseAssessment->course_id);
        if (is_null($assessmentType)) {
            return null;
        }

        return $courseAssessment = CourseAssessment::where(
            [
                ['assessment_id', $assessmentType->sub_assessment_id],
                ['course_id', $assessmentType->course_id],
            ]
        )->first();
    }

    private function storeThresholdForm(object $oldThresholdForm): ?ThresholdForm
    {
        if ($oldThresholdForm->default_workflow_id == 0 || is_null($oldThresholdForm->default_workflow_id) ||
        $oldThresholdForm->form_id == 0 || is_null($oldThresholdForm->form_id)) {
            return null;
        }

        $thresholdForm['id'] = $oldThresholdForm->threshold_id;
        $thresholdForm['category_course_id'] = $oldThresholdForm->category_course_id;
        $thresholdForm['form_id'] = $oldThresholdForm->form_id;
        $thresholdForm['default_workflow_id'] = $oldThresholdForm->default_workflow_id;
        $thresholdForm['origin_threshold_id'] = $this->getOriginThresholdId($oldThresholdForm);
        $thresholdForm['send_amount'] = $oldThresholdForm->threshold_value;
        $thresholdForm['form_start_at'] = $oldThresholdForm->form_start_day;
        $thresholdForm['form_write_at'] = $oldThresholdForm->form_write_days;
        $thresholdForm['deleted_at'] = ($oldThresholdForm->is_delete === 1) ? $oldThresholdForm->update_time : null;
        $thresholdForm['created_at'] = $oldThresholdForm->create_time;
        $thresholdForm['updated_at'] = $oldThresholdForm->update_time;
        echo $thresholdForm['id']."\n";

        DB::transaction(function () use ($thresholdForm) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            ThresholdForm::forceCreate($thresholdForm);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });

        return null;
    }

    private function getOriginThresholdId(object $oldThresholdForm): ?int
    {
        $originThresholdFormId = ($oldThresholdForm->origin_threshold_id == $oldThresholdForm->threshold_id) ? null : $oldThresholdForm->origin_threshold_id;
        $checkThresholdForm = ThresholdForm::find($originThresholdFormId);
        if (is_null($checkThresholdForm)) {
            return null;
        }

        return $originThresholdFormId;
    }

    private function storeIgnoreThresholdForm(object $oldIgnoreThresholdForm): ?IgnoreThresholdForm
    {
        $ignoreThresholdForm = new IgnoreThresholdForm();
        $ignoreThresholdForm->origin_threshold_id = $oldIgnoreThresholdForm->origin_threshold_id;
        $ignoreThresholdForm->user_id = $oldIgnoreThresholdForm->student_user_id;
        $ignoreThresholdForm['deleted_at'] = ($oldIgnoreThresholdForm->is_delete === 1) ?
            $oldIgnoreThresholdForm->update_time : null;
        $ignoreThresholdForm['created_at'] = $oldIgnoreThresholdForm->create_time;
        $ignoreThresholdForm['updated_at'] = $oldIgnoreThresholdForm->update_time;
        $ignoreThresholdForm->save();
        echo $ignoreThresholdForm->id."\n";

        return $ignoreThresholdForm;
    }

    private function storeManualForm(object $workflow): ?ManualForm
    {
        $count = $this->checkManualForm($workflow)->count();
        $manualForm = $this->checkManualFormToNewTable($workflow, $count);
        if (is_null($manualForm)) {
            if (is_null($workflow->batch_id)) {
                return null;
            }
            $newManualForm['title'] = $workflow->name;
            $newManualForm['training_program_id'] = $workflow->batch_id;
            $newManualForm['send_amount'] = $count;
            $newManualForm['form_id'] = $workflow->form_id;
            $newManualForm['form_start_at'] = 1;
            $newManualForm['form_write_at'] = $this->calculateFormWriteDate($workflow->start_date, $workflow->end_date);
            $newManualForm['deleted_at'] = ($workflow->is_delete === 1) ? $workflow->update_time : null;
            $newManualForm['created_at'] = $workflow->create_time;
            $newManualForm['updated_at'] = $workflow->update_time;

            return DB::transaction(function () use ($newManualForm) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                ManualForm::forceCreate($newManualForm);
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });
        }

        return $manualForm;
    }

    private function checkManualFormToNewTable(object $workflow, int $count): ?ManualForm
    {
        return ManualForm::where([
            ['training_program_id', $workflow->batch_id],
            ['send_amount', $count],
            ['form_id', $workflow->form_id],
            ['form_write_at', $this->calculateFormWriteDate($workflow->start_date, $workflow->end_date)],
        ])->first();
    }

    private function calculateFormWriteDate(string $startAt, string $endAt): int
    {
        $day = (strtotime($endAt) - strtotime($startAt)) / 86400;

        return $day === 0 ? 1 : $day;
    }

    private function storeWorkflow(object $workflow, int $dataId): Workflow
    {
        $createWorkflow['id'] = $workflow->ai_id;
        $createWorkflow['evaluatee'] = $workflow->user_id;
        $createWorkflow['title'] = $workflow->name;
        $createWorkflow['training_program_id'] = $workflow->batch_id ?? $workflow->batch_id;
        $createWorkflow['form_id'] = is_null($workflow->form_id) ? null : $workflow->form_id;
        $createWorkflow['unit_id'] = is_null($workflow->group_id) ? 1 : $workflow->group_id;
        $createWorkflow['type'] = $this->checkWorkflowType($workflow);
        $createWorkflow['data_id'] = $dataId;
        $createWorkflow['is_return'] = $workflow->state === 2 ? true : false;
        $createWorkflow['create_by'] = $workflow->create_user_id === -1 || is_null($workflow->create_user_id) ? 1 : $workflow->create_user_id;
        $createWorkflow['start_at'] = ($workflow->start_date == '0000-00-00') ? date('Y-m-d') : $workflow->start_date;
        $createWorkflow['end_at'] = ($workflow->end_date == '0000-00-00') ? date('Y-m-d') : $workflow->end_date;
        $createWorkflow['deleted_at'] = ($workflow->is_delete === 1) ? $workflow->update_time : null;
        $createWorkflow['created_at'] = $workflow->create_time;
        $createWorkflow['updated_at'] = $workflow->update_time;
        echo $workflow->id."\n";

        $newWorkflow = DB::transaction(function () use ($createWorkflow) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            return Workflow::forceCreate($createWorkflow);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
        $this->transferProcess($newWorkflow->id, $workflow->workflow_id);

        return $newWorkflow;
    }

    private function transferProcess(int $newWorkflowId, int $oldWorkflow): void
    {
        $processes = $this->getProcesses($oldWorkflow);
        foreach ($processes as $process) {
            if (isset($newProcess)) {
                $nextWorkflowId = $newProcess->id;
            } else {
                $nextWorkflowId = null;
            }
            $newProcess = $this->storeProcess($nextWorkflowId, $process, $newWorkflowId);
        }
    }

    private function storeProcess(?int $previousProcessId, object $oldProcess, int $workflowId): Process
    {
        $process['workflow_id'] = $workflowId;
        $process['is_default'] = $oldProcess->is_default;
        $process['type'] = $this->getTransferProcess($oldProcess->process_type);
        $process['state'] = $oldProcess->sign_state;
        $process['error_status'] = $this->getErrorStatus($oldProcess);
        $process['sign_by'] = $oldProcess->sign_user_id;
        $process['role'] = $oldProcess->role_id;
        $process['opinion'] = $oldProcess->opinion;
        $process['deleted_at'] = ($oldProcess->is_delete === 1) ? $oldProcess->update_time : null;
        $process['created_at'] = $oldProcess->create_time;
        $process['updated_at'] = $oldProcess->update_time;
        $newProcess = DB::transaction(function () use ($process) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            return Process::forceCreate($process);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });

        $findProcess = Process::find($previousProcessId);
        if (! is_null($findProcess)) {
            $findProcess->next_process_id = $newProcess->id;
            $findProcess->update();
        }

        return $newProcess;
    }

    private function getErrorStatus($oldProcess): int
    {
        // ProcessErrorStatusEnum
        if (is_null($oldProcess->role_id) || $oldProcess->role_id === 0) {
            return ProcessErrorStatusEnum::NO_SETTING_ROLE;
        }

        //TODO:check user is exist and authorize
        if (is_null(User::find($oldProcess->sign_user_id))) {
            return ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE;
        }

        return ProcessErrorStatusEnum::NORMAL;
    }

    private function getTransferProcess($type): ?int
    {
        switch ($type) {
            case 0:
                return ProcessTypeEnum::SINGLE;
                break;
            case 4:
                return ProcessTypeEnum::NOTIFY;
            case 5:
                return ProcessTypeEnum::FILL;
            case 6:
                return ProcessTypeEnum::EVALUATEE;
            case 7:
                return ProcessTypeEnum::ANONYMOUS;
            default:
                return null;
                break;
        }
    }

    private function getWorkflow(int $index): Collection
    {
        return DB::connection('raw')
                ->table('common_assessment_information')
                ->leftJoin(
                    'common_assessment_result',
                    'common_assessment_result.info_id',
                    '=',
                    'common_assessment_information.id'
                )
                ->join('common_assessment_result_user', function ($join) {
                    $join->on('common_assessment_result.ai_id', '=', 'common_assessment_result_user.ai_id')
                         ->where('common_assessment_result_user.ru_type', '=', 1);
                })
                ->leftJoin(
                    'common_assessment_workflow',
                    'common_assessment_workflow.ai_id',
                    '=',
                    'common_assessment_result.ai_id'
                )
                ->orderBy('id', 'asc')
                ->limit(100)
                ->offset(100 * $index++)
                ->get();
    }

    private function checkManualForm(object $workflow): Collection
    {
        return DB::connection('raw')
                ->table('common_assessment_information')
                ->join(
                    'common_assessment_result',
                    'common_assessment_result.info_id',
                    '=',
                    'common_assessment_information.id'
                )
                ->join(
                    'common_assessment_result_user',
                    'common_assessment_result.ai_id',
                    '=',
                    'common_assessment_result_user.ai_id'
                )
                ->join(
                    'common_assessment_workflow',
                    'common_assessment_workflow.ai_id',
                    '=',
                    'common_assessment_result.ai_id'
                )
                ->where('group_id', '=', $workflow->group_id)
                ->where('common_assessment_result.form_id', '=', $workflow->form_id)
                ->where('user_id', '=', $workflow->user_id)
                ->where('start_date', '=', $workflow->start_date)
                ->where('end_date', '=', $workflow->end_date)
                ->where('batch_id', '=', $workflow->batch_id)
                ->get();
    }

    private function getThresholdForm(int $index): Collection
    {
        return DB::connection('raw')
            ->table('common_course_threshold')
            ->limit(100)
            ->offset(100 * $index++)
            ->orderBy('threshold_id', 'asc')
            ->get();
    }

    private function getIgnoreThresholdForm(int $index): Collection
    {
        return DB::connection('raw')
            ->table('common_course_threshold_ignore_student')
            ->limit(100)
            ->offset(100 * $index++)
            ->orderBy('ignore_id', 'asc')
            ->get();
    }

    private function getProcesses(int $workflowId): Collection
    {
        return DB::connection('raw')
            ->table('common_assessment_workflow_process')
            ->join(
                'common_assessment_workflow_process_user',
                'common_assessment_workflow_process_user.process_id',
                '=',
                'common_assessment_workflow_process.process_id'
            )
            ->where('workflow_id', '=', $workflowId)
            ->orderBy('sequence', 'asc')
            ->get();
    }

    private function getCourseAssessmentId(int $informationId)
    {
        return DB::connection('raw')
            ->table('common_course_form_assessment_course')
            ->where('information_id', $informationId)
            ->first();
    }

    private function getAssessmentType(int $typeId, int $groupId, int $courseId)
    {
        return DB::connection('raw')
            ->table('common_course_assessment_sub_type')
            ->join(
                'common_course_assessment',
                'common_course_assessment.sub_assessment_id',
                '=',
                'common_course_assessment_sub_type.sub_assessment_id'
            )
            ->where('common_course_assessment_sub_type.type_id', 'form_'.$typeId)
            ->where('common_course_assessment_sub_type.group_id', $groupId)
            ->where('common_course_assessment.course_id', $courseId)
            ->first();
    }
}
