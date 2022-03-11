<?php

namespace App\Jobs;

use App\Models\Workflow\DefaultWorkflow;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use App\Services\Interfaces\IDefaultWorkflowService;
use App\Services\Interfaces\IProcessService;
use App\Services\Interfaces\IScheduleSendWorkflowFormService;
use App\Services\Interfaces\IThresholdFormService;
use App\Services\Workflow\DefaultWorkflowService;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\Enum\ProcessStateEnum;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use App\Services\Workflow\ProcessService;
use App\Services\Workflow\ScheduleSendWorkflowFormService;
use App\Services\Workflow\ThresholdFormService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWorkflowFormJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private IScheduleSendWorkflowFormService $scheduleSendWorkflowFormService;

    private IThresholdFormService $thresholdService;

    private IDefaultWorkflowService $defaultWorkflowService;

    private IProcessService $processService;

    public function __construct()
    {
        $this->scheduleSendWorkflowFormService = new ScheduleSendWorkflowFormService();

        $this->thresholdService = new ThresholdFormService();

        $this->defaultWorkflowService = new DefaultWorkflowService();

        $this->processService = new ProcessService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        while (true) {
            $queues = $this->scheduleSendWorkflowFormService->getSentForm(100);

            if ($queues->count() < 1) {
                break;
            }

            collect($queues)->map(function ($queue) {
                $this->mapWorkflowForm($queue);
                $queue->delete();
            });
        }
    }

    private function mapWorkflowForm(object $scheduleWorkflowForm): void
    {
        switch ($scheduleWorkflowForm->type) {
            case WorkflowTypeEnum::COURSE:
                break;
            case WorkflowTypeEnum::THRESHOLD:
                    $this->storeWorkflowToThreshold($scheduleWorkflowForm);
                break;
            case WorkflowTypeEnum::MANUAL:
                break;
        }
    }

    private function storeWorkflowToThreshold(object $scheduleWorkflowForm): void
    {
        $threshold = $this->thresholdService->getById($scheduleWorkflowForm->key_id);

        $defaultWorkflow = $this->defaultWorkflowService->getById($threshold->default_workflow_id);
        $newWorkflow = $this->storeWorkflow(
            $scheduleWorkflowForm,
            $threshold->form_id
        );
        $this->storeWorkflowProcess($newWorkflow->id, $defaultWorkflow);
    }

    private function storeWorkflow(object $scheduleWorkflowForm, int $formId): Workflow
    {
        $newWorkflow = new Workflow();
        $newWorkflow->evaluatee = $scheduleWorkflowForm->student_id;
        $newWorkflow->title = $scheduleWorkflowForm->title;
        $newWorkflow->type = WorkflowTypeEnum::THRESHOLD;
        $newWorkflow->data_id = $scheduleWorkflowForm->key_id;
        $newWorkflow->unit_id = $scheduleWorkflowForm->unit_id;
        $newWorkflow->form_id = $formId;
        $newWorkflow->create_by = $scheduleWorkflowForm->create_at;
        $newWorkflow->start_at = $scheduleWorkflowForm->start_at;
        $newWorkflow->end_at = $scheduleWorkflowForm->end_at;
        $newWorkflow->save();

        return $newWorkflow;
    }

    /**
     * threshold name : $assessment_info_name = $group_info['group_name'] . '-' . date('Y年m月', strtotime($write_start_date)) . '-' . $form_data['name'];
     * course name : $course_data['course_name'] . ' - ' . $course_form_assessment_type['type_name'].
     */
    //role , user_id
    private function storeWorkflowProcess(int $workflowId, DefaultWorkflow $defaultWorkflow): void
    {
        $processes = json_decode($defaultWorkflow->process);
        collect($processes)->map(function ($process) use ($workflowId) {
            $laseProcess = $this->processService->getByLastProcess($workflowId);
            $newProcess = new Process();
            $newProcess->workflow_id = $workflowId;
            $newProcess->type = $process->type;
            $newProcess->state = ProcessStateEnum::NO_START;
            $newProcess->role = $process->role;
            $newProcess->opinion = '';
            //TODO:判斷是否有符合這個角色的人員
            $newProcess->sign_by = is_null($process->user_id) ? null : 1;
            $newProcess->error_status = $this->checkProcessErrorStatus($newProcess->role, $newProcess->sign_by);
            $newProcess->save();
            if (! is_null($laseProcess)) {
                $laseProcess->next_process_id = $newProcess->id;
                $laseProcess->update();
            }
        });
    }

    private function checkProcessErrorStatus(?int $role_id, ?int $user_id): int
    {
        if ($role_id === 0 || is_null($role_id)) {
            return ProcessErrorStatusEnum::NO_SETTING_ROLE;
        }

        if ($user_id === 0) {
            return ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE;
        }

        return ProcessErrorStatusEnum::NORMAL;
    }
}
