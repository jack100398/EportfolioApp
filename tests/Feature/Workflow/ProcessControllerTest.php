<?php

namespace Tests\Feature\Workflow;

use App\Models\Auth\User;
use App\Models\NominalRole\NominalRole;
use App\Models\Workflow\Process;
use App\Services\Interfaces\IProcessService;
use App\Services\Interfaces\IWorkflowService;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\Enum\ProcessTypeEnum;
use App\Services\Workflow\ProcessService;
use App\Services\Workflow\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessControllerTest extends TestCase
{
    use RefreshDatabase;

    private IProcessService $processService;

    private IWorkflowService $workflowService;

    public function __construct()
    {
        parent::__construct();
        $this->processService = new ProcessService();
        $this->workflowService = new WorkflowService();
    }

    public function testReturnWorkflow()
    {
        $process = Process::factory()->create();
        $processIds = collect([1, 2, 3, 4, 5])->map(function ($key) use ($process) {
            $laseProcess = $this->processService->getByLastProcess($process->workflow_id);
            $newProcess = Process::factory()->state(['workflow_id'=>$process->workflow_id])->create();
            if (! is_null($laseProcess)) {
                $laseProcess->next_process_id = $newProcess->id;
                $laseProcess->update();
            }

            return $newProcess->id;
        });
        $this->patch(
            'api/process/return/'.$processIds->last()
        )->assertOk();
    }

    /**
     * 測試加簽.
     */
    public function testStore()
    {
        $process = Process::factory()->create();
        $user = User::factory()->create(['deleted_at'=>null]);
        $this->post(
            'api/process',
            [
                'process_id' => $process->id,
                'sign_by' => $user->id,
                'type' => ProcessTypeEnum::NOTIFY,
                'role' => NominalRole::factory()->create()->id,
            ]
        )->assertCreated();
    }

    /**
     * 測試加簽.
     */
    public function testAddAssignNotFound()
    {
        $process = Process::factory()->create();
        collect([1, 2, 3, 4, 5])->map(function ($key) use ($process) {
            $laseProcess = $this->processService->getByLastProcess($process->workflow_id);
            $newProcess = Process::factory()->state(['workflow_id'=>$process->workflow_id])->create();
            if (! is_null($laseProcess)) {
                $laseProcess->next_process_id = $newProcess->id;
                $laseProcess->update();
            }
        });
        $this->post(
            'api/process',
            [
                'process_id' => $process->id,
                'sign_by' => 1,
                'type' => ProcessTypeEnum::NOTIFY,
                'role' => NominalRole::factory()->create()->id,
            ]
        )->assertNotFound();
    }

    public function testIndex()
    {
        $process = Process::factory()->create();
        $this->json(
            'get',
            'api/process',
            ['workflow_id'=>$process->workflow_id]
        )->assertOk();
    }

    public function testIndexTOAnonymous()
    {
        $process = Process::factory()->create(['type'=>ProcessTypeEnum::ANONYMOUS]);

        $this->json(
            'get',
            'api/process',
            ['workflow_id'=>$process->workflow_id]
        )->assertOk();
    }

    public function testDestroy()
    {
        $process = Process::factory()->create();
        $this->delete(
            'api/process/'.$process->id
        )->assertNoContent();
    }

    public function testDisagree()
    {
        $process = Process::factory()->create();
        $processIds = collect([1, 2, 3, 4, 5])->map(function ($key) use ($process) {
            $laseProcess = $this->processService->getByLastProcess($process->workflow_id);
            $newProcess = Process::factory()->state(['workflow_id'=>$process->workflow_id])->create();
            if (! is_null($laseProcess)) {
                $laseProcess->next_process_id = $newProcess->id;
                $laseProcess->update();
            }

            return $newProcess->id;
        });
        $processIds = $processIds->push($process->id)->sort();
        $this->patch(
            'api/process/disagree/'.$process->workflow_id,
            [
                'process_id' => $processIds->last(),
                'bacK_process_id' => $processIds->first(),
            ]
        )->assertOk();

        $result = $this->processService->getByWorkflowId($process->workflow_id);
        $this->assertCount(12, $result);
    }

    public function testDisagreeByIdIsNotFound()
    {
        $this->patch(
            'api/process/disagree/11111',
            [
                'process_id' => 1,
                'bacK_process_id' => 1,
            ]
        )->assertNotFound();
    }

    public function testUpdate()
    {
        $process = Process::factory()->create();
        $this->put(
            'api/process/'.$process->id,
            [
                'opinion' => 'test',
            ]
        )->assertOk();
    }

    public function testUpdateSignBy()
    {
        $process = Process::factory()->create(['error_status' => ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE]);
        $this->patch(
            'api/process/updateSignBy/'.$process->id,
            [
                'sign_by' => User::factory()->create(['deleted_at'=>null])->id,
            ]
        )->assertOk();
    }

    public function testUpdateSignByIdIsNotFound()
    {
        $this->patch(
            'api/process/updateSignBy/111111',
            [
                'sign_by' => User::factory()->create(['deleted_at'=>null])->id,
            ]
        )->assertNotFound();
    }

    public function testReturnWorkflowByWorkflowIdIsNotFound()
    {
        $process = Process::factory()->create(['error_status' => ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE]);
        $this->workflowService->deleteById($process->workflow_id);
        $this->patch(
            'api/process/return/'.$process->id
        )->assertNotFound();
    }

    public function testReturnWorkflowByIdIsNotFound()
    {
        $this->patch(
            'api/process/return/11111'
        )->assertNotFound();
    }

    public function testUpdateByIdIsNotFound()
    {
        $this->put(
            'api/process/111111',
            [
                'opinion' => 'test',
            ]
        )->assertNotFound();
    }

    public function testUpdateRoleByIdIsNotFound()
    {
        $this->patch(
            'api/process/updateRole/11111',
            [
                'role' => 11111,
            ]
        )->assertNotFound();
    }

    public function testUpdateRole()
    {
        $process = Process::factory()->create(['error_status' => ProcessErrorStatusEnum::NO_SETTING_ROLE]);
        $this->patch(
            'api/process/updateRole/'.$process->id,
            [
                'role' => NominalRole::factory()->create()->id,
            ]
        )->assertOk();
    }

    public function testUpdateIsNotFoundRole()
    {
        $process = Process::factory()->create(['error_status' => ProcessErrorStatusEnum::NO_SETTING_ROLE]);
        $this->patch(
            'api/process/updateRole/'.$process->id,
            [
                'role' => 11111,
            ]
        )->assertNotFound();
    }

    public function testUpdateBatchModifyProcess()
    {
        $oldSignBy = User::factory()->create()->id;
        $newSignBy = User::factory()->create()->id;
        $processId = Process::factory()->create(['sign_by'=>$oldSignBy])->id;
        $this->patch('api/process/batch/update', [
            'old_sign_by' => $oldSignBy,
            'ids' => [$processId],
            'new_sign_by' => $newSignBy,
        ])->assertOk();
    }
}
