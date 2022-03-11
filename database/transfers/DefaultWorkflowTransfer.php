<?php

namespace Database\Transfers;

use App\Models\Workflow\DefaultWorkflow;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use DB;
use Exception;
use Illuminate\Support\Collection;
use Log;

class DefaultWorkflowTransfer
{
    public function transfer(): void
    {
        $index = 0;

        $start = microtime(true);

        while (true) {
            $defaultWorkflows = $this->getDefaultWorkflow($index++);
            if ($defaultWorkflows->count() < 1) {
                break;
            }
            $this->mapDefaultWorkflow($defaultWorkflows);
        }

        $time_elapsed_secs = microtime(true) - $start;
        echo $time_elapsed_secs;
    }

    private function mapDefaultWorkflow(Collection $defaultWorkflows): void
    {
        collect($defaultWorkflows)->map(function ($defaultWorkflow) {
            $this->storeDefaultWorkflow($defaultWorkflow);
        });
    }

    private function storeDefaultWorkflow(object $defaultWorkflow): void
    {
        try {
            $newDefaultWorkflow['id'] = $defaultWorkflow->default_workflow_id;
            $newDefaultWorkflow['title'] = $defaultWorkflow->title;
            $newDefaultWorkflow['unit_id'] = $defaultWorkflow->group_id;
            $newDefaultWorkflow['process'] = $this->transferDefaultProcess(
                $this->getDefaultProcess($defaultWorkflow->default_workflow_id)
            )->toJson();
            $newDefaultWorkflow['deleted_at'] = ($defaultWorkflow->is_delete === 1) ? $defaultWorkflow->update_time : null;
            $newDefaultWorkflow['created_at'] = $defaultWorkflow->create_time;
            $newDefaultWorkflow['updated_at'] = $defaultWorkflow->update_time;

            DB::transaction(function () use ($newDefaultWorkflow) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DefaultWorkflow::forceCreate($newDefaultWorkflow);
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });
            echo $newDefaultWorkflow['id']."\n";
        } catch (Exception $e) {
            Log::error($e->getMessage());
            echo 'error '.$newDefaultWorkflow['id']."\n";
        }
    }

    private function transferDefaultProcess(Collection $defaultProcesses): Collection
    {
        return collect($defaultProcesses)->each(function ($defaultProcess) {
            $defaultProcess->type = $this->switchProcessType($defaultProcess->type);

            return $defaultProcess;
        });
    }

    private function switchProcessType(int $processType)
    {
        switch ($processType) {
            case 0:
                return ProcessTypeEnum::SINGLE;
            case 4:
                return ProcessTypeEnum::NOTIFY;
            case 5:
                return ProcessTypeEnum::FILL;
            case 6:
                return ProcessTypeEnum::EVALUATEE;
            case 7:
                return ProcessTypeEnum::ANONYMOUS;
            default:
            throw new Exception('UnknownProcessType');
        }
    }

    private function getDefaultWorkflow(int $index): Collection
    {
        return DB::connection('raw')
                ->table('common_assessment_default_workflow')
                ->limit(100)
                ->offset(100 * $index++)
                ->orderBy('default_workflow_id', 'asc')
                ->get();
    }

    private function getDefaultProcess(int $workflowId): Collection
    {
        return DB::connection('raw')
                ->table('common_assessment_default_workflow_process')
                ->select(
                    'common_assessment_default_workflow_process.process_type as type',
                    'common_assessment_default_workflow_process_user.default_user_id as user_id',
                    'common_assessment_default_workflow_process_user.default_role_id as role'
                )
                ->join(
                    'common_assessment_default_workflow_process_user',
                    'common_assessment_default_workflow_process.default_process_id',
                    '=',
                    'common_assessment_default_workflow_process_user.default_process_id'
                )
                ->where('common_assessment_default_workflow_process.default_workflow_id', $workflowId)
                ->orderByDesc('common_assessment_default_workflow_process.sequence')
                ->get();
    }
}
