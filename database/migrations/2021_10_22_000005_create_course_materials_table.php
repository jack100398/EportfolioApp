<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('material_id')->constrained('materials');
            $table->string('description');
            $table->time('required_time')->nullable()->comment('最少閱覽時間');
            $table->timestamp('opened_at')->nullable()->comment('開放閱覽時間');
            $table->timestamp('ended_at')->nullable()->comment('終止閱覽時間');
            $table->foreignId('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_materials');
    }
}
