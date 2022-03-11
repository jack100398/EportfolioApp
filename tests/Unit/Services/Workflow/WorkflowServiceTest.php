<?php

namespace Tests\Unit\Services\Workflow;

use App\Models\Form\Form;
use App\Models\Form\FormUnit;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use App\Services\Interfaces\IWorkflowService;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\Enum\ProcessStateEnum;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use App\Services\Workflow\WorkflowService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    private IWorkflowService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new WorkflowService();
    }

    public function testGetById()
    {
        $id = Workflow::factory()->create()->id;
        $result = $this->service->getById($id);
        $this->assertTrue($result instanceof Workflow);
    }

    public function testGetByDataId()
    {
        Workflow::factory()->count(1)->state(['data_id'=>1])->create();
        $result = $this->service->getByDataId(1, WorkflowTypeEnum::THRESHOLD);
        $this->assertCount(1, $result);
    }

    public function testGetByWorkflowTypePagination()
    {
        Process::factory()->count(10)->create();

        collect(Form::orderByDesc('id')->get())->map(function ($form) {
            $formUnit = new FormUnit();
            $formUnit->form_id = $form->id;
            $formUnit->unit_id = 1;
            FormUnit::saved($formUnit);
        });
        $result = $this->service->getByWorkflowTypePagination(
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'state'=>'all',
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>date('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        );
        $this->assertCount(10, $result);
    }

    public function testGetByWorkflowProgressPagination()
    {
        Process::factory()->count(10)->state(['state'=>ProcessStateEnum::STARTED])->create();

        $result = $this->service->getByWorkflowTypePagination(
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'state'=>'progress',
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>date('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        );
        $this->assertCount(10, $result);
    }

    public function testGetByWorkflowEndPagination()
    {
        Process::factory()->count(10)->state(['state'=>ProcessStateEnum::END])->create();

        $result = $this->service->getByWorkflowTypePagination(
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'state'=>'end',
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>date('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        );
        $this->assertCount(10, $result);
    }

    public function testGetByIdWithProcess()
    {
        Workflow::factory()->state(['data_id'=>1])->create();
        $result = $this->service->getByIdWithProcess(1, WorkflowTypeEnum::THRESHOLD);
        $this->assertNotEmpty($result);
    }

    public function testGetErrorWorkflowPagination()
    {
        Process::factory()->count(10)->state(['error_status'=>ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE])->create();
        $result = $this->service->getErrorWorkflowPagination(
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'error_status'=>[ProcessErrorStatusEnum::NO_SETTING_ROLE, ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE],
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>date('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        );
        $this->assertCount(10, $result);
    }

    public function testGetWorkflowLateUserName()
    {
        $workflow = Workflow::factory()->state([
            'start_at'=> Carbon::now()->subDays(2)->format('Y-m-d'),
            'end_at'=> Carbon::now()->subDays(1)->format('Y-m-d'),
        ])->create();
        Process::factory()->state(['workflow_id'=>$workflow->id])->create();
        $result = $this->service->getWorkflowLateUser($workflow->id);
        $this->assertTrue($result instanceof Workflow);
    }

    public function testGetThresholdFormWorkflowMany()
    {
        $workflows = Workflow::factory()->count(10)->create();
        $dataIds = collect($workflows)->map(function ($workflow) {
            return $workflow->data_id;
        })->toArray();
        $result = $this->service->getThresholdFormWorkflowMany($dataIds);
        $this->assertCount(10, $result);
    }

    public function testGetByIds()
    {
        Workflow::get()->each(function ($workflow) {
            $workflow->delete();
        });
        Workflow::factory()->count(10)->create();
        $workflowIds = Workflow::pluck('id')->toArray();
        $result = $this->service->getByIds($workflowIds);
        $this->assertCount(10, $result);
    }

    public function testGetByIdWithForm()
    {
        $workflow = Workflow::factory()->create();
        $result = $this->service->getByIdWithForm($workflow->id);
        $this->assertTrue($result instanceof Workflow);
        $this->assertTrue($result->form instanceof Form);
    }
}
