<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_question_pivot', function (Blueprint $table) {
            $table->id()->comment('測驗作答紀錄關聯此ID，修改題目答案用');
            $table->foreignId('exam_id')->comment('紀錄每份測驗有哪些題目')->constrained('exams');
            $table->foreignId('question_id')->constrained('exam_questions');
            $table->integer('score')->comment('該題配分');
            $table->integer('sequence')->comment('出題排序');
            $table->unique(['exam_id', 'question_id']);
        });

        Schema::create('exam_folder_pivot', function (Blueprint $table) {
            $table->foreignId('exam_id')->comment('紀錄每份測驗用到那些題庫（隨機出題用）')->constrained('exams');
            $table->foreignId('folder_id')->constrained('exam_folders');
            $table->unique(['exam_id', 'folder_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_question_pivot');
        Schema::dropIfExists('exam_folder_pivot');
    }
}
