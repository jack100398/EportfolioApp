<?php

namespace Tests\Feature\Workflow;

use App\Models\Auth\User;
use App\Models\Course\CourseAssessment;
use App\Models\Form\Form;
use App\Models\NominalRole\NominalRole;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use App\Services\Interfaces\IProcessService;
use App\Services\Workflow\Enum\ProcessErrorStatusEnum;
use App\Services\Workflow\Enum\ProcessStateEnum;
use App\Services\Workflow\Enum\WorkflowTypeEnum;
use App\Services\Workflow\ProcessService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowControllerTest extends TestCase
{
    use RefreshDatabase;

    private IProcessService $processService;

    public function __construct()
    {
        parent::__construct();
        $this->processService = new ProcessService();
    }

    public function testIndex()
    {
        Process::factory()->count(10)->create();
        $this->json(
            'get',
            'api/workflow',
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'state'=>'all',
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>Carbon::now()->subDays(2)->format('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        )->assertOk()->assertJsonCount(10);
    }

    public function testIndexCourse()
    {
        $courseAssessment = CourseAssessment::factory()->create();
        $workflow = Workflow::factory()->create(
            [
                'type'=>WorkflowTypeEnum::COURSE,
                'data_id'=>$courseAssessment->id,
            ]
        );
        Process::factory()->create(['workflow_id'=>$workflow->id]);
        $this->json(
            'get',
            'api/workflow',
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'state'=>'all',
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>Carbon::now()->subDays(2)->format('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        )->assertOk();
    }

    public function testIndexGetEnd()
    {
        $processes = Process::factory()->count(2)->create(['state'=>ProcessStateEnum::END]);
        collect($processes)->map(function ($process) {
            $laseProcess = $this->processService->getByLastProcess($process->workflow_id);
            $newProcess = new Process();
            $newProcess->workflow_id = $process->workflow_id;
            $newProcess->type = 1;
            $newProcess->state = ProcessStateEnum::END;
            $newProcess->role = NominalRole::factory()->create()->id;
            $newProcess->sign_by = User::factory()->create(['deleted_at'=>null])->id;
            $newProcess->save();

            if (! is_null($laseProcess)) {
                $laseProcess->next_process_id = $newProcess->id;
                $laseProcess->update();
            }
        });
        $this->json(
            'get',
            'api/workflow',
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'state'=>'end',
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>Carbon::now()->subDays(2)->format('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        )->assertOk()->assertJsonCount(2);
    }

    public function testGetThresholdWorkflow()
    {
        $workflowId = Workflow::factory()->create()->data_id;
        $this->json(
            'get',
            'api/workflow/type/thresholdForm',
            ['thresholdFormIds'=>[$workflowId]]
        )->assertOk();
    }

    public function testIndexGetLaterName()
    {
        $workflows = Workflow::factory()->count(2)->create([
            'start_at'=> Carbon::now()->subDays(2)->format('Y-m-d'),
            'end_at'=> Carbon::now()->subDays(1)->format('Y-m-d'),
        ]);
        collect($workflows)->map(function ($workflow) {
            Process::factory()->create(['workflow_id'=>$workflow->id]);
        });
        $this->json(
            'get',
            'api/workflow',
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'state'=>'all',
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>Carbon::now()->subDays(3)->format('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        )->assertOk();
    }

    public function testIndexGetProgress()
    {
        $processes = Process::factory()->count(2)->create(['state'=>ProcessStateEnum::END]);
        collect($processes)->map(function ($process) {
            $laseProcess = $this->processService->getByLastProcess($process->workflow_id);
            $newProcess = new Process();
            $newProcess->workflow_id = $process->workflow_id;
            $newProcess->type = 1;
            $newProcess->state = ProcessStateEnum::STARTED;
            $newProcess->role = NominalRole::factory()->create()->id;
            $newProcess->sign_by = User::factory()->create(['deleted_at'=>null])->id;
            $newProcess->save();

            if (! is_null($laseProcess)) {
                $laseProcess->next_process_id = $newProcess->id;
                $laseProcess->update();
            }
        });
        $this->json(
            'get',
            'api/workflow',
            [
                'training_program_ids' => Workflow::orderByDesc('id')->get()->pluck('training_program_id')->toArray(),
                'types'=>WorkflowTypeEnum::TYPES,
                'state'=>'progress',
                'unit_ids'=>Workflow::orderByDesc('id')->get()->pluck('unit_id')->toArray(),
                'per_page'=>10,
                'form_ids'=>Form::orderByDesc('id')->get()->pluck('id')->toArray(),
                'start_at'=>Carbon::now()->subDays(2)->format('Y-m-d'),
                'end_at'=>date('Y-m-d'),
            ]
        )->assertOk();
    }

    public function testErrorIndex()
    {
        Process::factory()->count(10)->state(['error_status'=>ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE])->create();
        $this->json(
            'get',
            'api/workflow/error/index',
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
        )->assertOk();
    }

    public function testErrorIndexCourseIdIsNull()
    {
        $courseAssessment = CourseAssessment::factory()->create();
        $workflow = Workflow::factory()->create(
            [
                'type'=>WorkflowTypeEnum::COURSE,
                'data_id'=>$courseAssessment->id,
            ]
        );
        Process::factory()->create(['workflow_id'=>$workflow->id, 'error_status'=>ProcessErrorStatusEnum::NOT_FOUND_EVALUATEE]);

        $this->json(
            'get',
            'api/workflow/error/index',
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
        )->assertOk();
    }

    public function testShow()
    {
        $id = Process::factory()->create()->workflow_id;
        $this->get('api/workflow/'.$id)->assertOk();
    }

    public function testDestroy()
    {
        $id = Process::factory()->create()->workflow_id;
        $this->delete('api/workflow/'.$id)->assertNoContent();
    }

    public function testGetCanBatchModifyWorkflow()
    {
        $signBy = Process::factory()->create()->sign_by;
        $this->get('api/workflow/batch/'.$signBy)->assertOk();
    }
}
