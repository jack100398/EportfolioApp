<?php

namespace Tests\Feature\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramUnit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrainingProgramUnitControllerTest extends TestCase
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
        $programUnit = TrainingProgramUnit::factory()->create();
        $response = $this->get('/api/trainingProgram/unit/'.$programUnit->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $programUnit = TrainingProgramUnit::factory()->make()->toArray();
        $response = $this->post('/api/trainingProgram/unit', $programUnit);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $programUnit = TrainingProgramUnit::factory()->create();
        $response = $this->get('/api/trainingProgram/unit/'.$programUnit->id + 1);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $programUnit = TrainingProgramUnit::factory()->create();
        $response = $this->delete('/api/trainingProgram/unit/'.$programUnit->id);

        $response->assertNoContent();
    }
}
