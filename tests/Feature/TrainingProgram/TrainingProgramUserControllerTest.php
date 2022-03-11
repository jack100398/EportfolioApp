<?php

namespace Tests\Feature\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramUser;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrainingProgramUserControllerTest extends TestCase
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
        $programUser = TrainingProgramUser::factory()->create();
        $response = $this->get('/api/trainingProgram/user/'.$programUser->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $programUser = TrainingProgramUser::factory()->make()->toArray();
        $response = $this->post('/api/trainingProgram/user', $programUser);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $programUser = TrainingProgramUser::factory()->create();
        $response = $this->get('/api/trainingProgram/user/'.$programUser->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $programUser = TrainingProgramUser::factory()->create();
        $response = $this->patch('/api/trainingProgram/user/'.$programUser->id, [
            'phone_number' => 'newPhoneNumber',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $programUser = TrainingProgramUser::factory()->create();
        $response = $this->patch('/api/trainingProgram/user/'.$programUser->id + 1, [
            'phone_number' => 'newPhoneNumber',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $programUser = TrainingProgramUser::factory()->create();
        $response = $this->delete('/api/trainingProgram/user/'.$programUser->id);

        $response->assertNoContent();
    }
}
