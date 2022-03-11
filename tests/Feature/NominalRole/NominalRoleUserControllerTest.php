<?php

namespace Tests\Feature\NominalRole;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\NominalRole\NominalRole;
use App\Models\NominalRole\NominalRoleUser;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NominalRoleUserControllerTest extends TestCase
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
        $nominalRoleUser = NominalRoleUser::factory()->create();
        $response = $this->get('/api/nominalRoleUser/'.$nominalRoleUser->id);

        $response->assertOk();
    }

    public function testCanStore()
    {
        $data = [
            'user_id' => User::factory()->create()->id,
            'nominal_role_id' => NominalRole::factory()->create(['type' => NominalRole::TYPE_COURSE])->id,
            'morph_id' => Course::factory()->create()->id,
        ];

        $response = $this->post('/api/nominalRoleUser', $data);

        $this->assertTrue($response->json()['id'] > 0);
    }

    public function testShowCanReturnNotFound()
    {
        $nominalRoleUser = NominalRoleUser::factory()->create();
        $response = $this->get('/api/nominalRoleUser/'.$nominalRoleUser->id + 1);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $nominalRoleUser = NominalRoleUser::factory()->create();
        $response = $this->delete('/api/nominalRoleUser/'.$nominalRoleUser->id);

        $response->assertNoContent();
    }
}
