<?php

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use function PHPUnit\Framework\assertTrue;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ModuleSeeder::class);
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $user->syncRoles(Role::SUPER_ADMIN);
        Sanctum::actingAs($user, ['*']);
    }

    public function testShowRolePermissions()
    {
        $role = Role::create(['name' => 'test', 'readable_name' => '測試']);
        $permission = Permission::create(['name' => 'view.test']);

        $role->syncPermissions($permission);

        $this->get('/api/roles/'.$role->id.'/permissions')
            ->assertJsonStructure([
                '*'=>[
                    'name',
                    'unique_name',
                    'view',
                    'add',
                    'edit',
                    'delete',
                ],
            ]);
    }

    public function testUpdateRolePermissions()
    {
        $role = Role::create(['name' => 'test', 'readable_name' => '測試']);
        $viewPermission = Permission::create(['name' => 'view.test']);
        $addPermission = Permission::create(['name' => 'add.test']);
        $editPermission = Permission::create(['name' => 'edit.test']);
        $deletePermission = Permission::create(['name' => 'delete.test']);

        $data = [
            [
                'unique_name' => 'test',
                'view' => true,
                'add' => true,
                'edit' => true,
                'delete' => true,
            ],
        ];
        $this->putJson('/api/roles/'.$role->id.'/permissions', $data)
            ->assertNoContent();

        $this->assertTrue(
            $role->hasPermissionTo($viewPermission) &&
            $role->hasPermissionTo($addPermission) &&
            $role->hasPermissionTo($editPermission) &&
            $role->hasPermissionTo($deletePermission)
        );
    }

    public function testShowPermissions()
    {
        $this->get('/api/permissions')
            ->assertOk()
            ->assertJsonStructure([
                '*'=>[
                    'name',
                    'unique_name',
                    'view',
                    'add',
                    'edit',
                    'delete',
                ],
            ]);
    }

    public function testGetPermissionById()
    {
        $permission = Permission::create([
            'name' => 'view.users',
        ]);

        $this->get('/api/permissions/'.$permission->id)
            ->assertJsonStructure([
                'id',
                'name',
            ]);
    }

    public function testCreatePermission()
    {
        $viewPermission = Permission::create([
            'name' => 'view.courses',
        ]);
        $editPermission = Permission::create([
            'name' => 'edit.courses',
        ]);

        $this->post('/api/permissions', [
            'name' => 'Test Manager',
            'permissions' => [$viewPermission->name, $editPermission->name],
        ])->assertCreated();
    }

    public function testUpdatePermission()
    {
        $permission = Permission::create([
            'name' => 'delete.courses',
        ]);

        $this->put('/api/permissions/'.$permission->id, ['name' => 'Test Manager1']);

        $this->assertTrue(Permission::find($permission->id)->name == 'Test Manager1');
    }

    public function testDeletePermissionById()
    {
        $permission = Permission::create(['name' => 'view.courses']);

        $this->delete('/api/permissions/'.$permission->id);

        $this->assertEmpty(Permission::find($permission->id));
    }
}
