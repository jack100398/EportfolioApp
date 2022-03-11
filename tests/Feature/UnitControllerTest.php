<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamQuestion;
use App\Models\Unit;
use App\Services\UnitUserEnum;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UnitControllerTest extends TestCase
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
        $response = $this->get('/api/unit');

        $response->assertOk();
    }

    public function testCanShow()
    {
        $unit = Unit::factory()->create();
        $response = $this->get('/api/unit/'.$unit->id);

        $response->assertOk();
        $this->assertSame($unit->id, $response->json()['id']);
    }

    public function testCanStore()
    {
        $unit = Unit::factory()->make()->toArray();
        $response = $this->post('/api/unit/', $unit);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $unit = Unit::factory()->create();
        $response = $this->get('/api/unit/'.$unit->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $unit = Unit::factory()->create();
        $response = $this->patch('/api/unit/'.$unit->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
        $this->assertTrue($unit->refresh()->name === 'newName');
    }

    public function testUpdateCanReturnNotFound()
    {
        $unit = Unit::factory()->create();
        $response = $this->patch('/api/unit/'.$unit->id + 1, [
            'title' => 'newTitle',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $unit = Unit::factory()->create();
        $response = $this->delete('/api/unit/'.$unit->id);

        $response->assertNoContent();
    }

    public function testCanAddUser()
    {
        $data = [
            'unit_id' => Unit::factory()->create()->id,
            'user_id' => User::factory()->create(['deleted_at'=>null])->id,
            'type' => UnitUserEnum::DEFAULT,
        ];
        $response = $this->post('/api/unit/user', $data);

        // asserts
        $response->assertNoContent();
        $this->assertNotNull(
            Unit::find($data['unit_id'])
                ->users()
                ->where('id', $data['user_id'])
                ->first()
        );
    }
}
