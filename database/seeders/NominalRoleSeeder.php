<?php

namespace Database\Seeders;

use App\Models\NominalRole\NominalRole;
use Illuminate\Database\Seeder;

class NominalRoleSeeder extends Seeder
{
    /**
     * ROLE_ID_STUDENT = 1; // 學生
     * ROLE_ID_TEACHER = 2; // 課程教師
     * ROLE_ID_TUTOR = 3; // 其他導師
     * ROLE_ID_CLINICAL = 4; // 臨床教師
     * ROLE_ID_SUPERVISOR = 5; // 長期導師
     * ROLE_ID_SUPERVISOR_1 = 51; // 導師1
     * ROLE_ID_SUPERVISOR_2 = 52; // 導師2
     * ROLE_ID_DIRECTOR = 6; // 科主任
     * ROLE_ID_HOST = 7; // 計畫主持人
     * ROLE_ID_TEACH_LEADER = 8; // 教學負責人
     * ROLE_ID_CLASSMATE = 53; // 同儕.
     */
    public function run()
    {
        // $data = [
        //     ['id'=>1, 'type'=>NominalRole::TYPE_COURSE, 'name'=>'學生'],
        //     ['id'=>2, 'type'=>NominalRole::TYPE_COURSE, 'name'=>'課程教師'],
        //     ['id'=>3, 'type'=>NominalRole::TYPE_COURSE, 'name'=>'其他導師'],
        //     ['id'=>4, 'type'=>NominalRole::TYPE_TRAINING_PROGRAM_STEP, 'name'=>'臨床教師'],
        //     ['id'=>5, 'type'=>NominalRole::TYPE_TRAINING_PROGRAM_STEP, 'name'=>'長期導師'],
        //     ['id'=>6, 'type'=>NominalRole::TYPE_TRAINING_PROGRAM_UNIT, 'name'=>'訓練科室科主任'],
        //     ['id'=>7, 'type'=>NominalRole::TYPE_TRAINING_PROGRAM, 'name'=>'計畫主持人'],
        //     ['id'=>8, 'type'=>NominalRole::TYPE_TRAINING_PROGRAM, 'name'=>'教學負責人'],
        //     ['id'=>9, 'type'=>NominalRole::TYPE_TRAINING_PROGRAM_STEP, 'name'=>'導師1'],
        //     ['id'=>10, 'type'=>NominalRole::TYPE_TRAINING_PROGRAM_STEP, 'name'=>'導師2'],
        // ];
        // collect($data)->each(function ($row) {
        //     NominalRole::forceCreate([
        //         'id' => $row['id'],
        //         'type' => $row['type'],
        //         'name' => $row['name'],
        //         'is_active' => true,
        //     ]);
        // });
    }
}
