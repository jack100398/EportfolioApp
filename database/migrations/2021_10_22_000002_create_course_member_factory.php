<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseMemberFactory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses', 'id');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->unsignedBigInteger('role')->comment('課程權限 1.學生 2.協同教師 3.課程教師 4.課程管理者');
            $table->timestamp('joined_at')->useCurrent();
            $table->boolean('is_online_course')->comment('學生是否為報名數位課程');
            $table->foreignId('updated_by')->constrained('users', 'id');
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->boolean('state');
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
        Schema::dropIfExists('course_members');
    }
}
