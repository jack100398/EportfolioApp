<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseSurveyRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_survey_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answered_by')->constrained('users')->comment('填寫人');
            $table->foreignId('course_survey_id')->constrained('course_surveys')->comment('對應哪一個課程問卷');
            $table->integer('role_type')->comment('填寫人身分');
            $table->json('metadata')->comment('使用者填寫的答案');
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
        Schema::dropIfExists('course_survey_records');
    }
}
