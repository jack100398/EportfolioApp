<?php

namespace Database\Seeders;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name'=>Role::SUPER_ADMIN, 'readable_name'=>'超級管理者', 'description'=> '系統最高管理員，擁有所有權限']);
        Role::create(['name'=>Role::ADMIN, 'readable_name'=>'管理者']);
        Role::create(['name'=>Role::UNIT_MANAGER, 'readable_name'=>'單位管理者']);
        Role::create(['name'=>Role::TEACHER, 'readable_name'=>'教師']);
        Role::create(['name'=>Role::STUDENT, 'readable_name'=>'學生']);

        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles()
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            if ($role->name == Role::SUPER_ADMIN) {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions(Permission::where('name', 'LIKE', 'view.%')->get());
            }
        }
    }
}
