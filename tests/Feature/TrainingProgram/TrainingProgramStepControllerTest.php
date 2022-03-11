<?php

namespace Tests\Feature\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramUser;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrainingProgramStepControllerTest extends TestCase
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
        $programStep = TrainingProgramStep::factory()->create();
        $response = $this->get('/api/trainingProgram/step/'.$programStep->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $programStep = TrainingProgramStep::factory()->make()->toArray();
        $response = $this->post('/api/trainingProgram/step', $programStep);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $programStep = TrainingProgramStep::factory()->create();
        $response = $this->get('/api/trainingProgram/step/'.$programStep->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $programStep = TrainingProgramStep::factory()->create();
        $response = $this->patch('/api/trainingProgram/step/'.$programStep->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $programStep = TrainingProgramStep::factory()->create();
        $response = $this->patch('/api/trainingProgram/step/'.$programStep->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $programStep = TrainingProgramStep::factory()->create();
        $response = $this->delete('/api/trainingProgram/step/'.$programStep->id);

        $response->assertNoContent();
    }

    public function testCanGetUserSteps()
    {
        $userId = User::factory()->create(['deleted_at'=>null])->id;
        $programUserId = TrainingProgramUser::factory()->create(['user_id' => $userId]);
        TrainingProgramStep::factory(2)->create(['program_user_id' => $programUserId]);

        $response = $this->get("/api/trainingProgram/user/$userId/step");
        $response->assertOk();
    }
}
