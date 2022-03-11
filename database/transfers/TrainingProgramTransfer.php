<?php

namespace Database\Transfers;

use App\Models\DefaultCategory;
use App\Models\NominalRole\NominalRole;
use App\Models\NominalRole\NominalRoleUser;
use App\Models\TrainingProgram\OccupationalClass;
use App\Models\TrainingProgram\TrainingProgram;
use App\Models\TrainingProgram\TrainingProgramCategory;
use App\Models\TrainingProgram\TrainingProgramCourseShare;
use App\Models\TrainingProgram\TrainingProgramStep;
use App\Models\TrainingProgram\TrainingProgramStepTemplate;
use App\Models\TrainingProgram\TrainingProgramSync;
use App\Models\TrainingProgram\TrainingProgramUnit;
use App\Models\TrainingProgram\TrainingProgramUser;
use App\Models\Workflow\ThresholdForm;
use Generator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrainingProgramTransfer
{
    private string $connection = 'raw';

    private const NULL_DATETIME = '0000-00-00 00:00:00';

    private const NULL_DATE = '0000-00-00';

    /**
     * $this->nominalRoleMapping[ oldId ] = newId.
     */
    private array $nominalRoleMapping = [];

    /**
     * $this->programUnitMapping[ programId ][ unitId ] = ProgramUnitId.
     */
    private array $programUnitMapping = [];

    /**
     * $this->programUserMapping[ ts_id ] = trainingProgramId.
     */
    private array $programUserMapping = [];

    /**
     * $this->defaultCategoryMapping[ oldId ] = newId.
     *
     * @var array
     */
    private array $defaultCategoryMapping = [];

    public function index()
    {
        /*
         * [v]職類／計畫類別
         * [v]計畫本體
         * [v]單位／學生
         * [v]掛名角色
         * [v]計畫和單位角色
         * [v]站別／站別角色
         * [ ]附件
         * [v]架構
         * [v]訓練項目
         * [v]門檻
         * [?]課程 => CourseTransfer
         * [?]表單 => FormTransfer
         * [v]同步計畫
         * [v]站別樣板
         * [ ]跨計畫分享課程
         */
        DB::transaction(function () {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->transferOccupationClass();
            $this->transferTrainingProgram();
            $this->transferProgramUnit();
            $this->transferProgramUser();
            $this->transferProgramStep();
            $this->transferNominalRole();
            $this->transferNominalRoleUser();
            $this->transferAttachment(); // TODO: 沒有醫院正在用，加上還要轉移檔案
            $this->transferDefaultCategory();
            $this->transferProgramCategory();
            $this->transferSyncProgram();
            $this->transferThresholdForm();
            $this->transferStepTemplate();
            $this->transferCrossProgramCourses();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }

    public function transferOccupationClass(): void
    {
        $generator = $this->chunkOverDb('common_title_category');
        foreach ($generator as $row) {
            OccupationalClass::forceCreate([
                'id' => $row->id,
                'name' => $row->title,
                'parent_id' => $row->parent_id,
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
        }
    }

    public function transferTrainingProgram(): void
    {
        $generator = $this->chunkOverDb('common_training_schedule_batch');
        foreach ($generator as $row) {
            if ($row->update_time == self::NULL_DATETIME) {
                $row->update_time = Carbon::now();
            }
            if ($row->batch_end_date = self::NULL_DATETIME) {
                $row->batch_end_date = $row->batch_start_date;
            }

            TrainingProgram::forceCreate([
                'id'=> $row->batch_id,
                'year' => intval($row->year),
                'unit_id' => $row->group_id ?? 0,
                'occupational_class_id' => $row->bc_id ?? 0,
                'name' => $row->batch_name,
                'start_date' => $row->batch_start_date,
                'end_date' => $row->batch_end_date,
                'created_at' => $row->create_time,
                'updated_at' => $row->update_time,
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
        }
    }

    public function transferProgramUnit(): void
    {
        $generator = $this->chunkOverDb('common_training_group');
        foreach ($generator as $row) {
            $data = TrainingProgramUnit::forceCreate([
                'id' => $row->id,
                'training_program_id' => $row->batch_id,
                'unit_id' => $row->group_id,
                'deleted_at' => $row->is_delete ? Carbon::now() : null,
            ]);

            $this->programUnitMapping[$row->batch_id][$row->group_id] = $data->id;
        }
    }

    public function transferProgramUser(): void
    {
        $generator = $this->chunkOverDb('common_training_schedule');
        foreach ($generator as $row) {
            if ($row->user_id <= 0) { // why???
                continue;
            }
            $data = TrainingProgramUser::forceCreate([
                'id'=> $row->ts_id,
                'training_program_id' => $row->batch_id,
                'user_id' => $row->user_id,
                'phone_number' => $row->cell_num ?? '',
                'group_name' => $row->student_group_name ?? '',
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
            $this->programUserMapping[$row->ts_id] = $row->batch_id;
        }
    }

    public function transferProgramStep(): void
    {
        $generator = $this->chunkOverDb('common_training_schedule_step');
        foreach ($generator as $row) {
            if ($row->schedule_step_start_date == self::NULL_DATE) {
                continue;
            }

            $trainingProgramId = $this->programUserMapping[$row->ts_id] ?? null;
            $programUnitId = $trainingProgramId
                ? $this->programUnitMapping[$trainingProgramId][$row->step_group] ?? null
                : null;

            $deleted_at = $row->is_delete
                ? $row->delete_time ?? $row->update_time ?? Carbon::now()
                : null;

            TrainingProgramStep::forceCreate([
                'id' => $row->step_id,
                'program_unit_id' => $programUnitId,
                'program_user_id' => $row->ts_id,
                'name' => $row->step_name,
                'start_date' => $row->schedule_step_start_date,
                'end_date' => $row->schedule_step_end_date,
                'remarks' => $row->remark ?? '',
                'deleted_at' => $deleted_at,
            ]);
        }
    }

    public function transferNominalRoleUser(): void
    {
        // 1. 計畫角色
        $generator = $this->chunkOverDb('common_training_schedule_batch');
        foreach ($generator as $row) {
            if ($row->host_user_id) {
                NominalRoleUser::create([
                    'user_id' => $row->host_user_id,
                    'nominal_role_id' => 4,
                    'roleable_type' => TrainingProgram::class,
                    'roleable_id' => $row->batch_id,
                ]);
            }
            if ($row->tea_leader_id) {
                NominalRoleUser::create([
                    'user_id' => $row->tea_leader_id,
                    'nominal_role_id' => 5,
                    'roleable_type' => TrainingProgram::class,
                    'roleable_id' => $row->batch_id,
                ]);
            }
        }
        // 2. 單位角色
        // 科主任
        $generator = $this->chunkOverDb('common_training_group');
        foreach ($generator as $row) {
            if ($row->is_delete) {
                continue;
            }
            $programUnitId = $this->programUnitMapping[$row->batch_id][$row->group_id] ?? null;
            if ($programUnitId && $row->director_user_id) {
                NominalRoleUser::create([
                    'user_id' => $row->director_user_id,
                    'nominal_role_id' => 3,
                    'roleable_type' => TrainingProgramUnit::class,
                    'roleable_id' => $programUnitId,
                ]);
            }
        }
        // 科室教學負責人
        $generator = $this->chunkOverDb('common_training_group_role');
        foreach ($generator as $row) {
            if ($row->is_delete) {
                continue;
            }
            $programUnitId = $this->programUnitMapping[$row->batch_id][$row->group_id] ?? null;
            if ($programUnitId) {
                NominalRoleUser::create([
                    'user_id' => $row->user_id,
                    'nominal_role_id' => 4,
                    'roleable_type' => TrainingProgramUnit::class,
                    'roleable_id' => $programUnitId,
                ]);
            }
        }
        // 3. 站別角色
        $generator = $this->chunkOverDb('common_training_schedule_step_role', 5000);
        foreach ($generator as $row) {
            if ($row->is_delete) {
                continue;
            }
            NominalRoleUser::create([
                'user_id' => $row->user_id,
                'nominal_role_id' => $this->nominalRoleMapping[$row->role_id],
                'roleable_type' => TrainingProgramStep::class,
                'roleable_id' => $row->step_id,
            ]);
        }
    }

    private function transferNominalRole(): void
    {
        // 這部分的資料在原DB就為寫死的。
        $data = [
            ['id' => 1, 'type' => NominalRole::TYPE_COURSE, 'name' => '學生'],
            ['id' => 2, 'type' => NominalRole::TYPE_COURSE, 'name' => '課程教師'],
            ['id' => 3, 'type' => NominalRole::TYPE_TRAINING_PROGRAM_UNIT, 'name' => '科主任'],
            ['id' => 4, 'type' => NominalRole::TYPE_TRAINING_PROGRAM_UNIT, 'name' => '科室教學負責人'],
            ['id' => 5, 'type' => NominalRole::TYPE_TRAINING_PROGRAM, 'name' => '計畫主持人'],
            ['id' => 6, 'type' => NominalRole::TYPE_TRAINING_PROGRAM, 'name' => '教學負責人'],
            ['id' => 7, 'type' => NominalRole::TYPE_TRAINING_PROGRAM_STEP, 'name' => '導師1'],
            ['id' => 8, 'type' => NominalRole::TYPE_TRAINING_PROGRAM_STEP, 'name' => '導師2'],
        ];
        collect($data)->each(function ($row) {
            NominalRole::forceCreate([
                'id' => $row['id'],
                'type' => $row['type'],
                'name' => $row['name'],
                'is_active' => true,
            ]);
        });

        $this->nominalRoleMapping = [
            1 => 1, // 學生
            2 => 2, // 課程教師
            6 => 3, // 科主任
            // 4 為科室教學負責人，但是每間醫院的ID不同
            7 => 4, // 計畫主持人
            8 => 5, // 教學負責人
        ];

        // 可以人工建立的只有站別角色
        $generator = $this->chunkOverDb('role');
        foreach ($generator as $row) {
            if ($row->role_type != 1) {
                continue;
            }
            $data = NominalRole::create([
                'name' => $row->role_name,
                'type' => NominalRole::TYPE_TRAINING_PROGRAM_STEP,
                'is_active' => $row->is_active,
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
            $this->nominalRoleMapping[$row->role_id] = $data->id;
        }
    }

    public function transferAttachment(): void
    {
    }

    /**
     * 先複製架構分類，再複製架構，避免ID跑位.
     *
     * @return void
     */
    public function transferDefaultCategory(): void
    {
        $generator = $this->chunkOverDb('common_default_course_category');
        $models = collect();
        foreach ($generator as $row) {
            $defaultCategory = DefaultCategory::forceCreate([
                'id' => $row->id,
                'parent_id' =>$row->parent_id,
                'school_year' => 0,
                'unit_id' => 0,
                'name' => $row->name,
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
            $models->push(['type_id' => $row->type_id, 'model' => $defaultCategory]);
        }

        $generator = $this->chunkOverDb('common_default_category_type');
        foreach ($generator as $row) {
            $defaultCategory = DefaultCategory::create([
                'parent_id' => null,
                'school_year' => $row->year ?? 0,
                'unit_id' => $row->group_id ?? 0,
                'name' => $row->type_name,
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
            $this->defaultCategoryMapping[$row->id] = $defaultCategory->id;
        }

        $models->each(function ($model) {
            $category = $model['model'];
            $typeId = $model['type_id'];
            if ($category->parent_id == null) {
                $category->parent_id = $this->defaultCategoryMapping[$typeId];
                $category->save();
            }
        });
    }

    /**
     * 先複製訓練項目，再複製訓練分類.
     *
     * @return void
     */
    public function transferProgramCategory(): void
    {
        $generator = $this->chunkOverDb('common_category_course', 5000);
        $trainingItems = collect();
        $categories = [];
        foreach ($generator as $row) {
            $category = TrainingProgramCategory::forceCreate([
                'id' => $row->category_course_id,
                'parent_id' => $row->category_id, // 直接存 category_id 來找對應的分類
                'training_program_id' => 0, // 有分類才知道屬於哪個計畫
                'unit_id' => $row->group_id ?? 0,
                'default_category_id' => null,
                'is_training_item' => true,
                'name' => $row->category_course_name,
                'sort' => $row->sort,
                'created_by' => $row->create_user_id,
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
            $trainingItems->push($category);
        }

        $generator = $this->chunkOverDb('common_category', 5000);
        foreach ($generator as $row) {
            $defaultCategoryId = isset($this->defaultCategoryMapping[$row->default_category_id])
                ? $this->defaultCategoryMapping[$row->default_category_id]
                : null;
            $category = TrainingProgramCategory::create([
                'parent_id' => $row->belong_category_id,
                'training_program_id' => $row->batch_id,
                'unit_id' => $row->group_id ?? 0,
                'default_category_id' => $defaultCategoryId,
                'is_training_item' => false,
                'name' => $row->category_name,
                'sort' => $row->sort,
                'created_by' => $row->create_user_id,
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
            $categories[$row->category_id]['category_id'] = $category->id;
            $categories[$row->category_id]['training_program_id'] = $row->batch_id;
        }

        $trainingItems->each(function (TrainingProgramCategory $model) use ($categories) {
            if (! isset($categories[$model->parent_id]['training_program_id'])) {
                $model->forceDelete(); // 刪掉有問題的資料

                return true;
            }
            $model->training_program_id = $categories[$model->parent_id]['category_id'];
            $model->parent_id = $categories[$model->parent_id]['training_program_id'];
            $model->save();
        });
    }

    public function transferSyncProgram(): void
    {
        $generator = $this->chunkOverDb('common_training_schedule_batch_sync');
        foreach ($generator as $row) {
            TrainingProgramSync::create([
                'from_training_program_id' => $row->sync_from_batch_id,
                'to_training_program_id'   => $row->sync_to_batch_id,
            ]);
        }
    }

    public function transferThresholdForm(): void
    {
        $generator = $this->chunkOverDb('common_course_threshold', 5000);
        foreach ($generator as $row) {
            if ($row->threshold_type != 3) {
                continue;
            }
            ThresholdForm::forceCreate([
                'id' => $row->threshold_id,
                'program_category_id' => $row->category_course_id,
                'default_workflow_id' => $row->default_workflow_id,
                'origin_threshold_id' => $row->origin_threshold_id,
                'form_id' => $row->form_id,
                'send_amount' => $row->threshold_value,
                'form_start_at' => $row->form_start_day,
                'form_write_at' => $row->form_write_days,
                'deleted_at' => $row->is_delete ? $row->update_time : null,
            ]);
        }
    }

    public function transferStepTemplate(): void
    {
        $generator = $this->chunkOverDb('common_training_schedule_step_template');
        foreach ($generator as $row) {
            if (! isset($this->programUnitMapping[$row->batch_id][$row->group_id])) {
                continue;
            }
            $programUnitId = $this->programUnitMapping[$row->batch_id][$row->group_id];
            TrainingProgramStepTemplate::create([
                'training_program_id'=>$row->batch_id,
                'program_unit_id'=>$programUnitId,
                'days'=>$row->days,
                'sequence'=>$row->sequence,
            ]);
        }
    }

    public function transferCrossProgramCourses(): void
    {
        $generator = $this->chunkOverDb('common_course_share');
        foreach ($generator as $row) {
            if ($row->is_delete) {
                continue;
            }
            TrainingProgramCourseShare::create([
                'course_id' => $row->course_id,
                'program_category_id' => $row->category_course_id,
            ]);
        }
    }

    private function chunkOverDb(string $table, $count = 1000): Generator
    {
        $offset = 0;
        while (true) {
            $rows = $this->db($table)->offset($offset * $count)->limit($count)->get();
            echo "Chunking $table... $offset\n";
            if ($rows->count() == 0) {
                return;
            }
            foreach ($rows as $row) {
                yield $row;
            }
            $offset++;
        }
    }

    private function db(string $table): Builder
    {
        return DB::connection($this->connection)->table($table);
    }
}
