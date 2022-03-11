<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = new User();
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'superadmin@clouder.com.tw';
        $superAdmin->username = 'superadmin';
        $superAdmin->password = bcrypt('superadmin');
        $superAdmin->save();
        $superAdmin->assignRole(Role::SUPER_ADMIN);

        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@clouder.com.tw';
        $admin->username = 'admin';
        $admin->password = bcrypt('admin123');
        $admin->save();
        $admin->assignRole(Role::ADMIN);
    }
}
