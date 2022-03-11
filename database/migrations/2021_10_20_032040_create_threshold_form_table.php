<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThresholdFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threshold_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_category_id'); // 外鍵新增在CreateTrainingProgramCategoriesTable
            $table->foreignId('default_workflow_id')->constrained('default_workflow');
            $table->foreignId('form_id')->constrained('form');
            $table->foreignId('origin_threshold_id')->nullable(true)->constrained('threshold_form');
            $table->integer('send_amount');
            $table->integer('form_start_at');
            $table->integer('form_write_at');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('ignore_threshold_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_threshold_id')->constrained('threshold_form');
            $table->foreignId('user_id')->constrained('users');
            $table->softDeletes();
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
        Schema::dropIfExists('ignore_threshold_form');
        Schema::dropIfExists('threshold_form');
    }
}
