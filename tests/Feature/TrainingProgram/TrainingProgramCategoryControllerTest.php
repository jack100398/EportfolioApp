<?php

namespace Tests\Feature\TrainingProgram;

use App\Models\Auth\User;
use App\Models\DefaultCategory;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramCategory;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrainingProgramCategoryControllerTest extends TestCase
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
        $category = TrainingProgramCategory::factory()->create();
        $response = $this->get('/api/trainingProgram/category/'.$category->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $category = TrainingProgramCategory::factory()->make()->toArray();
        $response = $this->post('/api/trainingProgram/category', $category);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $category = TrainingProgramCategory::factory()->create();
        $response = $this->get('/api/trainingProgram/category/'.$category->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $category = TrainingProgramCategory::factory()->create();
        $response = $this->patch('/api/trainingProgram/category/'.$category->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $category = TrainingProgramCategory::factory()->create();
        $response = $this->patch('/api/trainingProgram/category/'.$category->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $category = TrainingProgramCategory::factory()->create();
        $response = $this->delete('/api/trainingProgram/category/'.$category->id);

        $response->assertNoContent();
    }

    public function testCanGetByTrainingProgramAndUnitId()
    {
        $category = TrainingProgramCategory::factory()->create();

        $response = $this->get("/api/trainingProgram/$category->training_program_id/category/$category->unit_id");

        $response->assertOk();
    }
}
