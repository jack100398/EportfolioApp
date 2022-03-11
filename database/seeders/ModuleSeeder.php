<?php

namespace Database\Seeders;

use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\Auth\UserController;
use App\Models\Admin\Module;
use App\Models\Auth\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->systemPreferences();
    }

    private function systemPreferences(): void
    {
        // The parent of modules
        $parent = Module::create(['name' => '系統設定']);

        // The unique_name usually used as table name in the database
        collect([
            [
                'unique_name'=>'users',
                'name' => '使用者管理',
                'controller' => UserController::class,
            ],
            [
                'unique_name'=>'audit_trails',
                'name' => '日誌查看',
                'controller' => AuditTrailController::class,
            ],
        ])->each(function ($module) use ($parent) {
            $module['parent_id'] = $parent->id;
            $module['is_enabled'] = true;
            Module::create($module);
        });
    }

    private function course(): void
    {
    }

    private function exam(): void
    {
    }

    private function assessment(): void
    {
    }
}
