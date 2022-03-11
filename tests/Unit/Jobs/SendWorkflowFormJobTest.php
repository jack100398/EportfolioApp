<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendWorkflowFormJob;
use App\Models\Workflow\ScheduleSendWorkflowForm;
use App\Models\Workflow\Workflow;
use App\Services\Interfaces\IWorkflowService;
use App\Services\Workflow\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendWorkflowFormJobTest extends TestCase
{
    use RefreshDatabase;

    private IWorkflowService $workflowService;

    public function __construct()
    {
        parent::__construct();
        $this->workflowService = new WorkflowService();
    }

    public function testJob()
    {
        ScheduleSendWorkflowForm::factory()->state(['start_at'=>date('Y-m-d')])->count(10)->create();
        $job = new SendWorkflowFormJob();
        $job->handle();
        $workflows = Workflow::get();
        $this->assertCount(10, $workflows);
    }
}
