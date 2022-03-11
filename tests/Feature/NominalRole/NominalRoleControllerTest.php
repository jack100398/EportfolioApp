<?php

namespace Tests\Feature\NominalRole;

use App\Models\Auth\User;
use App\Models\NominalRole\NominalRole;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NominalRoleControllerTest extends TestCase
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
        $response = $this->get('/api/nominalRole');

        $response->assertOk();
    }

    public function testCanShow()
    {
        $nominalRole = NominalRole::factory()->create();
        $response = $this->get('/api/nominalRole/'.$nominalRole->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $nominalRole = NominalRole::factory()->make()->toArray();
        $response = $this->post('/api/nominalRole', $nominalRole);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $nominalRole = NominalRole::factory()->create();
        $response = $this->get('/api/nominalRole/'.$nominalRole->id + 1);

        $response->assertNotFound();
    }

    public function testCanUpdate()
    {
        $nominalRole = NominalRole::factory()->create();
        $response = $this->patch('/api/nominalRole/'.$nominalRole->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $nominalRole = NominalRole::factory()->create();
        $response = $this->patch('/api/nominalRole/'.$nominalRole->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $nominalRole = NominalRole::factory()->create();
        $response = $this->delete('/api/nominalRole/'.$nominalRole->id);

        $response->assertNoContent();
    }
}
