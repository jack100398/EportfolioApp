<?php

namespace Tests\Unit\Services\Workflow;

use App\Models\Auth\User;
use App\Models\Workflow\ThresholdForm;
use App\Models\Unit;
use App\Models\Workflow\ScheduleSendWorkflowForm;
use App\Services\Interfaces\IScheduleSendWorkflowFormService;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use App\Services\Workflow\ScheduleSendWorkflowFormService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleSendWorkflowFormServiceTest extends TestCase
{
    use RefreshDatabase;

    private IScheduleSendWorkflowFormService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ScheduleSendWorkflowFormService();
    }

    public function testGetMany()
    {
        ScheduleSendWorkflowForm::factory()->count(10)->create();
        $result = $this->service->getMany(10);
        $this->assertCount(10, $result);
    }

    public function testGetSentForm()
    {
        ScheduleSendWorkflowForm::factory()->state(['start_at'=>date('Y-m-d')])->count(10)->create();
        $result = $this->service->getSentForm(10);
        $this->assertCount(10, $result);
    }

    public function testGetQueueForm()
    {
        $userId = User::factory()->create(['deleted_at'=>null])->id;
        $thresholdId = ThresholdForm::factory()->create()->id;
        $result = ScheduleSendWorkflowForm::factory()->state(['student_id'=>$userId,
            'key_id'=>$thresholdId, ])->create();
        $this->service->getQueueForm($thresholdId, $userId);
        $this->assertTrue($result instanceof ScheduleSendWorkflowForm);
    }

    public function testGetByOne()
    {
        ScheduleSendWorkflowForm::factory()->create();
        $result = $this->service->getByOne();
        $this->assertTrue($result instanceof ScheduleSendWorkflowForm);
    }

    public function testStore()
    {
        $users = User::factory()->count(10)->create(['deleted_at'=>null]);
        $schedulesWorkflowForms = collect($users)->map(function ($user) {
            $schedulesWorkflowForm = new ScheduleSendWorkflowForm();
            $schedulesWorkflowForm->key_id = ThresholdForm::factory()->create()->id;
            $schedulesWorkflowForm->title = 'test';
            $schedulesWorkflowForm->unit_id = Unit::factory()->create()->id;
            $schedulesWorkflowForm->create_at = User::factory()->create(['deleted_at'=>null])->id;
            $schedulesWorkflowForm->start_at = date('Y-m-d');
            $schedulesWorkflowForm->end_at = date('Y-m-d', strtotime('+1 day'));
            $schedulesWorkflowForm->type = WorkflowTypeEnum::THRESHOLD;
            $schedulesWorkflowForm->student_id = $user->id;
            $schedulesWorkflowForm->save();

            return $schedulesWorkflowForm;
        });

        $this->assertCount(10, $schedulesWorkflowForms);
    }
}
