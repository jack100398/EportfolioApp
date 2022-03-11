<?php

namespace Database\Seeders;

use App\Models\Admin\Module;
use App\Models\Auth\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Module::whereNotNull('unique_name')
            ->pluck('unique_name')
            ->each(function ($unique_name) {
                Permission::create(['name'=>Permission::ADD.$unique_name]);
                Permission::create(['name'=>Permission::EDIT.$unique_name]);
                Permission::create(['name'=>Permission::DELETE.$unique_name]);
                Permission::create(['name'=>Permission::VIEW.$unique_name]);
            });
    }
}
