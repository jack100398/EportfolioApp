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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->user = User::factory()->make();
        $this->user->deleted_at = null;
        $this->user->save();
    }

    public function testLoginSuccess()
    {
        $this->post('/api/login', [
            'username' => $this->user->username,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('token')
                    ->etc()
            );
    }

    public function testLogout()
    {
        Sanctum::actingAs($this->user, ['*']);

        $this->post('/api/logout')
            ->assertNoContent();
    }

    public function testLoginFail()
    {
        $this->post('/api/login', [
            'username' => 'admin',
            'password' => 'my987',
        ])
            ->assertUnauthorized()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('errors.message', 'The provided credentials are incorrect.')
                    ->etc()
            );
    }

    public function testRegistered()
    {
        $password = Str::random();
        $this->post('/api/register', [
            'name' => 'asu87',
            'email' => 'asu87@clouder.com.tw',
            'username' => 'asu87',
            'password' =>$password,
            'password_confirmation' => $password,
        ])
            ->assertCreated()
            ->assertJsonStructure(['id']);
    }

    public function testIfNoAuthentication()
    {
        $this->get('/api/users?size=10')
            ->assertUnauthorized();
    }
}
