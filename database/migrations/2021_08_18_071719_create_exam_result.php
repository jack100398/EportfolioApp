<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams');
            $table->foreignId('user_id')->comment('受考學生ID');
            $table->json('metadata')->comment('作答紀錄');
            $table->integer('score')->nullable()->comment('分數，null: 尚未評分');
            $table->boolean('is_marked')->default(false)->comment('是否已經評分');
            $table->boolean('is_finished')->default(false)->comment('是否作答完成');
            $table->date('start_time')->comment('作答開始時間');
            $table->date('end_time')->nullable()->comment('作答結束時間');
            $table->ipAddress('source_ip')->comment('作答人IP');
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
        Schema::dropIfExists('exam_results');
    }
}
