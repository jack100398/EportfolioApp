<?php

namespace Tests\Unit\Form;

use App\Models\Auth\User;
use App\Models\Form\Form;
use App\Models\Form\FormWriteRecord;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use App\Services\Form\Enum\FormWriteRecordFlagEnum;
use App\Services\Interfaces\IWorkflowService;
use App\Services\Workflow\WorkflowService;
use Database\Factories\Form\FormWriteResultFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormWriteRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    private IWorkflowService $workflowService;

    public function __construct()
    {
        parent::__construct();
        $this->workflowService = new WorkflowService();
    }

    public function testShow()
    {
        $formWriteRecord = FormWriteRecord::factory()->create();
        $this->get('api/formWriteRecord/'.$formWriteRecord->id)->assertOk();
    }

    public function testDestroy()
    {
        $formWriteRecord = FormWriteRecord::factory()->create();
        $this->delete('api/formWriteRecord/'.$formWriteRecord->id)->assertNoContent();
    }

    public function testGetResultFormWriteRecord()
    {
        $formWriteRecord = FormWriteRecord::factory()->create(['flag'=>FormWriteRecordFlagEnum::RESULT]);
        $this->get('api/formWriteRecord/detail/'.$formWriteRecord->workflow_id)->assertOk();
    }

    public function testStore()
    {
        $process = Process::factory()->create();
        $workflow = $this->workflowService->getByIdWithForm($process->workflow_id);
        $result = $this->generateResult($workflow->form);

        $this->post('api/formWriteRecord', [
            'process_id' => $process->id,
            'user_id' => $process->sign_by,
            'result' => $result,
            'flag' => FormWriteRecordFlagEnum::TEMP,
        ])->assertCreated();
    }

    public function testStoreResultNotWrite()
    {
        $process = Process::factory()->create();
        $workflow = $this->workflowService->getByIdWithForm($process->workflow_id);
        $form = Form::find($workflow->form_id);
        $questions = json_decode($form->questions);
        $testQuestions = collect($questions)->map(function ($questionTypes) {
            if (isset($questionTypes->attributes->questions)) {
                $questionTypes->attributes->questions = collect($questionTypes->attributes->questions)->map(function ($questionType) {
                    if (isset($questionType->attributes->require) && $questionType->attributes->require === false) {
                        $questionType->attributes->require = true;
                    }

                    return $questionType;
                });
            }

            return $questionTypes;
        });
        $form->questions = json_encode($testQuestions);
        $form->update();
        $results = $this->generateResult($workflow->form);
        $results = collect($results)->map(function ($result) {
            return collect($result)->map(function ($question) {
                return '';
            })->toArray();
        })->toArray();
        $this->post('api/formWriteRecord', [
            'process_id' => $process->id,
            'user_id' => $process->sign_by,
            'result' => $results,
            'flag' => FormWriteRecordFlagEnum::RESULT,
        ])->assertNotFound();
    }

    public function testStoreNotFoundRole()
    {
        $process = Process::factory()->create(['role'=>null]);
        $workflow = $this->workflowService->getByIdWithForm($process->workflow_id);
        $result = $this->generateResult($workflow->form);

        $this->post('api/formWriteRecord', [
            'process_id' => $process->id,
            'user_id' => $process->sign_by,
            'result' => $result,
            'flag' => FormWriteRecordFlagEnum::TEMP,
        ])->assertNotFound();
    }

    public function testStoreNotFoundWorkflow()
    {
        $process = Process::factory()->create();
        Workflow::find($process->workflow_id)->delete();
        $this->post('api/formWriteRecord', [
            'process_id' => $process->id,
            'user_id' => User::factory()->create(['deleted_at'=>null])->id,
            'result' => [123],
            'flag' => FormWriteRecordFlagEnum::TEMP,
        ])->assertNotFound();
    }

    private function generateResult(Form $form): array
    {
        $factory = new FormWriteResultFactory();

        return $factory->makeResult(json_decode($form->questions));
    }
}
