<?php

namespace Tests\Feature\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStepTemplate;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrainingProgramStepTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function testCanShow()
    {
        $stepTemplate = TrainingProgramStepTemplate::factory()->create();
        $response = $this->get('/api/trainingProgram/step/template/'.$stepTemplate->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $stepTemplate = TrainingProgramStepTemplate::factory()->make()->toArray();
        $response = $this->post('/api/trainingProgram/step/template', $stepTemplate);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $stepTemplate = TrainingProgramStepTemplate::factory()->create();
        $response = $this->get('/api/trainingProgram/step/template/'.$stepTemplate->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $stepTemplate = TrainingProgramStepTemplate::factory()->create();
        $response = $this->patch('/api/trainingProgram/step/template/'.$stepTemplate->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $stepTemplate = TrainingProgramStepTemplate::factory()->create();
        $response = $this->patch('/api/trainingProgram/step/template/'.$stepTemplate->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $stepTemplate = TrainingProgramStepTemplate::factory()->create();
        $response = $this->delete('/api/trainingProgram/step/template/'.$stepTemplate->id);

        $response->assertNoContent();
    }

    public function testCanGetByTrainingProgramId()
    {
        $program = TrainingProgram::factory()->has(TrainingProgramStepTemplate::factory(3), 'stepsTemplate')->create();
        $response = $this->get("/api/trainingProgram/$program->id/step/template");

        $response->assertOk();
    }
}
