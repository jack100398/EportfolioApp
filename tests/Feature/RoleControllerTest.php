<?php

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $user->syncRoles(Role::SUPER_ADMIN);
        Sanctum::actingAs($user, ['*']);
    }

    public function testShowRoles()
    {
        $this->get('/api/roles')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*'=>[
                        'id',
                        'name',
                        'readable_name',
                        'description',
                        'guard_name',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function testGetRoleById()
    {
        $role = Role::create([
            'name' => 'Test Manager',
            'readable_name'=>'測試管理者',
        ]);

        $this->get('/api/roles/'.$role->id)
            ->assertJsonStructure([
                'id',
                'name',
                'readable_name',
                'description',
                'guard_name',
                'created_at',
                'updated_at',
            ]);
    }

    public function testCreateRole()
    {
        $viewPermission = Permission::create([
            'name' => 'view.courses',
            'guard_name' => 'api',
        ]);
        $editPermission = Permission::create([
            'name' => 'edit.courses',
            'guard_name' => 'api',
        ]);

        $this->post('/api/roles', [
            'name' => 'Test Manager',
            'readable_name'=>'測試管理者',
            'description' => '測試管理者',
            'permissions' => [$viewPermission->name, $editPermission->name],
        ])->assertCreated();
    }

    public function testUpdateRole()
    {
        $role = Role::create([
            'name' => 'Test Manager',
            'readable_name'=>'測試管理者',
        ]);

        $this->put('/api/roles/'.$role->id, [
            'name' => 'Test Manager1',
            'description' => '測試管理者',
            'readable_name'=>'測試管理者1',
        ]);

        $role = Role::find($role->id);

        $this->assertTrue(
            $role->name == 'Test Manager1' && $role->readable_name == '測試管理者1'
        );
    }

    public function testDeleteRoleById()
    {
        $role = Role::create([
            'name' => 'Test Manager',
            'readable_name'=>'測試管理者',
        ]);

        $this->delete('/api/roles/'.$role->id);

        $this->assertEmpty(Role::find($role->id));
    }
}
