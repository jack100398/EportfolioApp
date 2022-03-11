<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseStudentAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_student_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('course_assessment_id')->constrained('course_assessments')->comment('對應之課程評核');
            $table->foreignId('student_id')->constrained('users');
            $table->integer('state')->comment('0:未通過,1:通過,2:不通過');
            $table->boolean('is_teacher_process')->comment('是否為教師待處理');
            $table->boolean('is_student_process')->comment('是否為學生待處理');
            $table->boolean('is_direct_pass')->comment('是否為 不須處理/直接通過');
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
        Schema::dropIfExists('course_student_assessments');
    }
}
