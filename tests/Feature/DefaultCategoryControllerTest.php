<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\DefaultCategory;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Models\Unit;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DefaultCategoryControllerTest extends TestCase
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

    public function testCanShowIndex()
    {
        $response = $this->get('/api/defaultCategory');

        $response->assertOk();
    }

    public function testCanShow()
    {
        $category = DefaultCategory::factory()->create();
        $response = $this->get('/api/defaultCategory/'.$category->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $category = DefaultCategory::factory()->make()->toArray();
        $response = $this->post('/api/defaultCategory', $category);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $category = DefaultCategory::factory()->create();
        $response = $this->get('/api/defaultCategory/'.$category->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $category = DefaultCategory::factory()->create();
        $response = $this->patch('/api/defaultCategory/'.$category->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $category = DefaultCategory::factory()->create();
        $response = $this->patch('/api/defaultCategory/'.$category->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $category = DefaultCategory::factory()->create();
        $response = $this->delete('/api/defaultCategory/'.$category->id);

        $response->assertNoContent();
    }
}
