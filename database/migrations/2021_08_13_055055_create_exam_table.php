<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('exam_folders')
                ->comment('父資料夾ID');
            $table->integer('type')->comment('1:官方題庫, 2:個人題庫');
            $table->foreignId('created_by')->comment('題庫擁有者');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')
                ->nullable()
                ->constrained('exam_folders')
                ->comment('所屬題庫ID，null:測驗修改過的題目');
            $table->text('context')->comment('題目內文');
            $table->json('metadata')->comment('答案選項、正解');
            $table->text('answer_detail')->comment('詳解');
            $table->integer('type')->comment('題目類型：選擇/是非/問答題');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('invigilator')->comment('監考人員');
            $table->dateTime('start_time')->nullable()->comment('測驗開始時間');
            $table->dateTime('end_time')->nullable()->comment('測驗結束時間');
            $table->dateTime('original_start_time')->nullable()->comment('原本的測驗開始時間，紀錄是否有修改測驗做補考過');
            $table->dateTime('original_end_time')->nullable()->comment('原本的測驗結束時間，紀錄是否有修改測驗做補考過');
            $table->boolean('is_answer_visible')->comment('學生是否看的到題目答案');
            $table->integer('scoring')->comment('計分方式');
            $table->integer('passed_score')->comment('測驗及格分數');
            $table->integer('total_score')->comment('測驗總分');
            $table->integer('question_type')->comment('出題順序');
            $table->json('random_parameter')->comment('隨機出題參數');
            $table->integer('limit_times')->comment('可測驗的次數，0:無限制');
            $table->time('answer_time')->nullable()->comment('作答時間');
            $table->foreignId('created_by')->comment('建立測驗的人');
            $table->boolean('is_template')->comment('是否為公版測驗');
            $table->foreignId('course_id')->nullable()->comment('所屬課程,若非課程測驗則為null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exam_folders');
    }
}
