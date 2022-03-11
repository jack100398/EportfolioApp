<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseFactory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->foreignId('program_category_id')->constrained('training_program_categories');
            $table->foreignId('default_category_id')->nullable()->constrained('default_categories');
            $table->foreignId('course_target')->constrained('course_targets');
            $table->foreignId('unit_id');
            $table->string('course_name');
            $table->text('course_remark')->comment('課程備註');
            $table->string('place')->nullable();
            $table->boolean('auto_update_students')->comment('自動為符合條件的學生報名');
            $table->boolean('open_signup_for_student')->comment('開放學生自己報名');
            $table->json('metadata');
            $table->boolean('is_compulsory')->comment('是否為必修課');
            $table->unsignedBigInteger('course_mode')->comment('課程種類 0:一般課程 1:影音課程');
            $table->dateTime('course_form_send_at')->useCurrent()->nullable();
            $table->dateTime('start_at')->useCurrent()->nullable();
            $table->dateTime('end_at')->useCurrent()->nullable();
            $table->dateTime('signup_start_at')->useCurrent()->nullable();
            $table->dateTime('signup_end_at')->useCurrent()->nullable();
            $table->foreignId('created_by');
            $table->integer('updated_by');
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->boolean('is_notified')->comment('是否通知學生老師');
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
        Schema::dropIfExists('courses');
    }
}
