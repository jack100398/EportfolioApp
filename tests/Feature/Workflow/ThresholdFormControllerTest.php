<?php

namespace Tests\Feature\Workflow;

use App\Models\Form\Form;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\Workflow\DefaultWorkflow;
use App\Models\Workflow\ThresholdForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThresholdFormControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowIndex()
    {
        $categoryId = TrainingProgramCategory::factory()->create()->id;
        ThresholdForm::factory()->state(['program_category_id'=>$categoryId])->create();

        $this->json(
            'GET',
            '/api/threshold/',
            ['programCategoryId'=>$categoryId]
        )->assertOk();
    }

    public function testShow()
    {
        $programCategoryId = TrainingProgramCategory::factory()->create()->id;
        $threshold = ThresholdForm::factory()->state(['program_category_id'=>$programCategoryId])->create();
        $this->get('/api/threshold/'.$threshold->id)->assertOk();
    }

    public function testStore()
    {
        $defaultWorkflowId = DefaultWorkflow::factory()->create()->id;
        $formId = Form::factory()->create()->id;

        $this->post('/api/threshold', [
            'program_category_id'=>TrainingProgramCategory::factory()->create()->id,
            'default_workflow_id'=>$defaultWorkflowId,
            'form_id'=>$formId,
            'send_amount'=>1,
            'form_start_at'=>1,
            'form_write_at'=>1,
        ])
        ->assertCreated();
    }

    public function testStoreNotFound()
    {
        $this->post('/api/threshold', [
            'program_category_id'=>TrainingProgramCategory::factory()->create()->id,
            'default_workflow_id'=>111111111,
            'form_id'=>11111111,
            'send_amount'=>1,
            'form_start_at'=>1,
            'form_write_at'=>1,
        ])
        ->assertNotFound();
    }

    public function testUpdate()
    {
        $categoryId = TrainingProgramCategory::factory()->create()->id;
        $defaultWorkflowId = DefaultWorkflow::factory()->create()->id;
        $formId = Form::factory()->create()->id;
        $threshold = $this->createThreshold();
        $this->put('/api/threshold/'.$threshold->id, [
            'program_category_id'=>$categoryId,
            'default_workflow_id'=>$defaultWorkflowId,
            'form_id'=>$formId,
            'send_amount'=>1,
            'form_start_at'=>1,
            'form_write_at'=>1,
        ])
        ->assertOk();
    }

    public function testUpdateNotFound()
    {
        $this->put('/api/threshold/111111', [
            'program_category_id'=>1,
            'default_workflow_id'=>1111111,
            'form_id'=>1111111111,
            'send_amount'=>1,
            'form_start_at'=>1,
            'form_write_at'=>1,
        ])
        ->assertNotFound();
    }

    public function testDestroy()
    {
        $threshold = $this->createThreshold();
        $this->delete('/api/threshold/'.$threshold->id)->assertNoContent();
    }

    private function createThreshold(): ThresholdForm
    {
        return ThresholdForm::factory()->state([
            'program_category_id'=>TrainingProgramCategory::factory()->create()->id,
            'form_start_at'=>1,
            'form_write_at'=>1,
        ])->create();
    }
}
