<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseHistoryFactory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('back_type')->comment('1:開課紀錄 2:修改紀錄');
            $table->json('request')->comment('此次 開/改 課程,前端發的request');
            $table->foreignId('course_id')->constrained('courses');
            $table->integer('year');
            $table->integer('program_category_id');
            $table->integer('default_category_id')->nullable();
            $table->foreignId('unit_id');
            $table->string('course_name');
            $table->string('course_remark');
            $table->string('place');
            $table->boolean('auto_update_students')->comment('自動為符合條件的學生報名');
            $table->boolean('open_signup_for_student')->comment('開放學生自己報名');
            $table->json('metadata');
            $table->boolean('is_compulsory')->comment('是否為必修課');
            $table->unsignedBigInteger('course_mode')->comment('課程種類 0:一般課程 1:影音課程');
            $table->timestamp('course_form_send_at')->useCurrent();
            $table->timestamp('start_at')->useCurrent();
            $table->timestamp('end_at')->useCurrent();
            $table->integer('overdue_type')->nullable()->comment('逾期開課 null:非逾期 1:修正課程 2:逾期建課 3:臨時開課 4:其他原因');
            $table->text('overdue_description')->nullable()->comment('逾期開課原因描述');
            $table->timestamp('signup_start_at')->useCurrent();
            $table->timestamp('signup_end_at')->useCurrent();
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
        Schema::dropIfExists('course_histories');
    }
}
