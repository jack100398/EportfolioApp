<?php

namespace Tests\Feature;

use App\Models\Admin\Module;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Database\Seeders\ModuleSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ModuleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ModuleSeeder::class);

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function testGetUsers()
    {
        $this->get('/api/modules')
            ->assertOk();
    }

    // public function testGetUserById()
    // {
    //     $user = User::factory()->create();

    //     $this->get('/api/users/'.$user->id)
    //         ->assertOk()
    //         ->assertJson(
    //             fn (AssertableJson $json) => $json
    //                 ->where('id', $user->id)
    //                 ->missing('password')
    //                 ->etc()
    //     );
    // }

    // public function testCreateUser()
    // {
    //     $this->post('/api/users', [
    //         'name' => 'my520',
    //         'email' => 'my520@example.com',
    //         'password' => '520my520',
    //         'roles' => [Role::SUPER_ADMIN],
    //     ])->assertCreated();
    // }

    // public function testUpdateUserEmail()
    // {
    //     $user = User::factory()->create();

    //     $this->put('/api/users/'.$user->id, [
    //         'name' => 'my520',
    //         'email' => '520my@clouder.com.tw',
    //         'roles' => [Role::TEACHER],
    //         'password' => '520my520',
    //     ])->assertNoContent();

    //     $this->get('/api/users/'.$user->id)
    //         ->assertOk()
    //         ->assertJson(
    //             fn (AssertableJson $json) => $json
    //                 ->where('email', '520my@clouder.com.tw')
    //                 ->etc()
    //     );
    // }

    // public function testSoftDeleteUser()
    // {
    //     $user = User::factory()->create();

    //     $this->delete('/api/users/'.$user->id)->assertNoContent();

    //     $this->get('/api/users/'.$user->id)
    //         ->assertNotFound();
    // }
}
