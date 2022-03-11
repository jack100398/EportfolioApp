<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\Exam\Exam;
use App\Models\Form\Form;
use App\Models\Form\FormUnit;
use App\Models\Form\FormWriteRecord;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ModuleSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        Form::factory()->count(50)->create();
        FormUnit::factory()->count(50)->create();
        FormWriteRecord::factory()->count(50)->create();
        Course::factory(10)->create();
        User::factory()->count(50)->create()
        ->each(function (User $user) {
            $user->assignRole(Role::STUDENT);
        });
    }
}
