<?php

namespace Tests\Feature\TrainingProgram;

use App\Models\Auth\User;
use App\Models\TrainingProgram\OccupationalClass;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramStep;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OccupationalClassControllerTest extends TestCase
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
        OccupationalClass::factory(3)->create(['parent_id'=>null]);
        $response = $this->get('/api/occupationalClass/');

        $response->assertOk();
    }

    public function testCanShow()
    {
        $class = OccupationalClass::factory()->create();
        $response = $this->get('/api/occupationalClass/'.$class->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $class = OccupationalClass::factory()->make()->toArray();
        $response = $this->post('/api/occupationalClass', $class);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $class = OccupationalClass::factory()->create();
        $response = $this->get('/api/occupationalClass/'.$class->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $class = OccupationalClass::factory()->create();
        $response = $this->patch('/api/occupationalClass/'.$class->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $class = OccupationalClass::factory()->create();
        $response = $this->patch('/api/occupationalClass/'.$class->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $class = OccupationalClass::factory()->create();
        $response = $this->delete('/api/occupationalClass/'.$class->id);

        $response->assertNoContent();
    }
}
