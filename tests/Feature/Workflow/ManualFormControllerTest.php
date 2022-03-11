<?php

namespace Tests\Feature\Workflow;

use App\Models\Form\Form;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\Workflow\DefaultWorkflow;
use App\Models\Workflow\ManualForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualFormControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStore()
    {
        $this->post(
            '/api/manualForm/',
            [
                'title'=>'test',
                'training_program_id'=>TrainingProgram::factory()->create()->id,
                'form_id' => Form::factory()->create()->id,
                'default_workflow_id'=>DefaultWorkflow::factory()->create()->id,
                'send_amount'=>1,
                'form_start_at'=>1,
                'form_write_at'=>1,
            ]
        )->assertCreated();
    }

    public function testShow()
    {
        $this->get(
            'api/manualForm/'.ManualForm::factory()->create()->id
        )->assertOk();
    }

    public function testDestroy()
    {
        $this->delete('api/manualForm/'.ManualForm::factory()->create()->id)->assertNoContent();
    }

    public function testGetByProgram()
    {
        $this->get(
            'api/manualForm/program/'.ManualForm::factory()->create()->id
        )->assertOk();
    }
}
