<?php

namespace Tests\Feature;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
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

    public function testGetUsers()
    {
        User::factory(10)->create();

        $this->get('/api/users?size=10')
            ->assertOk();
    }

    public function testGetLoginInformation()
    {
        $this->post('/api/users/info')
        ->assertJsonStructure([
            'name',
            'roles',
        ]);
    }

    public function testFilterUsers()
    {
        User::factory(10)->create();
        $user = User::factory()->make();
        $user->username = '6864f389d9876436bc8778ff071d1b6c';
        $user->save();

        $this->get('/api/users?size=10&filter=6864f389d9876436bc8778ff071d1b6c')
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('total', 1)
                    ->etc()
            )
            ->assertOk();
    }

    public function testGetUserById()
    {
        $user = User::factory()->create();

        $this->get('/api/users/'.$user->id)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('id', $user->id)
                    ->missing('password')
                    ->etc()
            );
    }

    public function testCreateUser()
    {
        $user = User::factory()->make();
        $this->post('/api/users', [
            'name' => 'my520',
            'email' => 'my520@example.com',
            'username' => 'my520',
            'password' => '520my520',
            'roles' => [Role::SUPER_ADMIN],
        ])->assertCreated();
    }

    public function testUpdateUserEmail()
    {
        $user = User::factory()->make();
        $user->deleted_at = null;
        $user->save();

        $user->roles = Role::first();
        $user->email = '520my@clouder.com.tw';

        $this->put('/api/users/'.$user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles,
            'password' => '520my520',
        ])->assertNoContent();

        $this->put('/api/users/'.$user->id, $user->toArray())->assertNoContent();

        $this->get('/api/users/'.$user->id)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('email', '520my@clouder.com.tw')
                    ->etc()
            );
    }

    public function testSoftDeleteUser()
    {
        $user = User::factory()->make();
        $user->deleted_at = null;
        $user->save();

        $this->delete('/api/users/'.$user->id)->assertNoContent();

        $deleted_at = $this->get('/api/users/'.$user->id)->json('deleted_at');
        $this->assertNotNull($deleted_at);
    }
}
