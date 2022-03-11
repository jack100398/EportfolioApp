<?php

use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingPrograms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 訓練計畫
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->comment('學年度');
            $table->foreignId('unit_id')->constrained('units')->comment('計畫所屬單位');
            $table->foreignId('occupational_class_id')->comment('職類->計畫分類'); // 外鍵新增在 CreateOccupationalClasses
            $table->string('name')->comment('計畫名稱');
            $table->dateTime('start_date')->comment('計畫開始日期');
            $table->dateTime('end_date')->comment('計畫結束日期');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('training_program_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained('training_programs')->comment('所屬計畫');
            $table->foreignId('unit_id')->comment('計劃科室');
            $table->softDeletes();
        });

        Schema::create('training_program_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained('training_programs')->comment('所屬計畫');
            $table->foreignId('user_id')->comment('計劃學生');
            $table->string('phone_number')->comment('手機簡碼');
            $table->string('group_name')->comment('組別');
            $table->softDeletes();
        });

        Schema::create('training_program_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_unit_id')->nullable()->constrained('training_program_units')->comment('null代表院外科室');
            $table->foreignId('program_user_id')->constrained('training_program_users');
            $table->string('name')->comment('站別名稱（院外科室會需要）');
            $table->date('start_date')->comment('站別開始時間');
            $table->date('end_date')->comment('站別結束時間');
            $table->string('remarks')->comment('站別備註');
            $table->softDeletes();
        });

        // 附件功能
        Schema::create('training_program_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained('training_programs');
            $table->foreignId('file_id')->nullable()->constrained('files')->comment('附加檔案ID，null代表該檔案是一個連結');
            $table->string('url')->comment('連結');
            $table->timestamps();
        });

        // 同步計畫
        Schema::create('training_program_syncs', function (Blueprint $table) {
            $table->foreignId('from_training_program_id')->constrained('training_programs');
            $table->foreignId('to_training_program_id')->constrained('training_programs');
            $table->timestamps();
        });
        Schema::create('training_program_step_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained('training_programs');
            $table->foreignId('program_unit_id')->constrained('training_program_units');
            $table->integer('days');
            $table->integer('sequence');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_program_step_templates');
        Schema::dropIfExists('training_program_syncs');
        Schema::dropIfExists('training_program_attachments');
        Schema::dropIfExists('training_program_steps');
        Schema::dropIfExists('training_program_users');
        Schema::dropIfExists('training_program_units');
        Schema::dropIfExists('training_programs');
    }
}
