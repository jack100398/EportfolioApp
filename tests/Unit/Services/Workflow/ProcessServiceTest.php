<?php

namespace Tests\Unit\Services\Workflow;

use App\Models\Auth\User;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use App\Services\Interfaces\IProcessService;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\ProcessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessServiceTest extends TestCase
{
    use RefreshDatabase;

    private IProcessService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ProcessService();
    }

    public function testGetByWorkflowId()
    {
        $workflowId = Workflow::factory()->create()->id;
        Process::factory()->state(['workflow_id'=>$workflowId])->create();
        $result = $this->service->getByWorkflowId($workflowId);
        $this->assertNotEmpty($result);
    }

    public function testGetByErrorWorkflow()
    {
        $process = Process::factory()->create(['error_status' => ProcessErrorStatusEnum::NO_SETTING_ROLE]);
        $result = $this->service->getByErrorWorkflow($process->id, ProcessErrorStatusEnum::NO_SETTING_ROLE);
        $this->assertTrue($result instanceof Process);
    }

    public function testGetById()
    {
        $process = Process::factory()->create();
        $result = $this->service->getById($process->id);
        $this->assertTrue($result instanceof Process);
    }

    public function testGetNextProcess()
    {
        $nextProcessId = Process::factory()->create()->id;
        Process::factory()->state(['next_process_id'=>$nextProcessId])->create();
        $result = $this->service->getNextProcess($nextProcessId);
        $this->assertTrue($result instanceof Process);
    }

    public function testByDefaultProcess()
    {
        $process = Process::factory()->create();
        $result = $this->service->getByDefaultProcess($process->workflow_id);
        $this->assertCount(1, $result);
    }

    public function testGetByLastProcess()
    {
        $process = Process::factory()->create();
        $result = $this->service->getByLastProcess($process->workflow_id);
        $this->assertTrue($result instanceof Process);
    }

    public function testSequence()
    {
        $process = Process::factory()->create();
        $nextProcess = Process::factory()->state(['workflow_id'=>$process->workflow_id])->create();
        $process->next_process_id = $nextProcess->id;
        $process->update();
        $sequences = $this->service->getProcessSequence($process->workflow_id, [$process->id, $nextProcess->id]);
        $this->assertCount(2, $sequences);
    }

    public function testBelowAllProcess()
    {
        $process = Process::factory()->create();
        $nextProcess = Process::factory()->state(['workflow_id'=>$process->workflow_id])->create();
        $process->next_process_id = $nextProcess->id;
        $process->update();
        $sequences = $this->service->getProcessSequence($process->workflow_id, [$process->id, $nextProcess->id]);
        $result = $this->service->getByBelowAllProcess($process->workflow_id, $sequences[0], $sequences[1] - $sequences[0] + 1);
        $this->assertCount(2, $result);
    }

    public function testGetNoStartProcess()
    {
        $signBy = User::factory()->create()->id;
        Process::factory()->count(10)->create(['sign_by'=>$signBy]);
        $result = $this->service->getNoStartProcess($signBy);
        $this->assertCount(10, $result);
    }

    public function testGetCanUpdateProcess()
    {
        $signBy = User::factory()->create()->id;
        Process::factory()->count(10)->create(['sign_by'=>$signBy]);
        $result = $this->service->getCanUpdateProcess(
            $signBy,
            Process::select('id')->get()->pluck('id')->toArray()
        );
        $this->assertCount(10, $result);
    }

    public function testGetPreviousProcess()
    {
        $process = Process::factory()->create();
        $nextProcess = Process::factory()->create(['workflow_id'=>$process->workflow_id]);
        $process->next_process_id = $nextProcess->id;
        $process->update();
        $result = $this->service->getPreviousProcess($nextProcess->id);
        $this->assertTrue($result->id === $process->id);
    }
}
