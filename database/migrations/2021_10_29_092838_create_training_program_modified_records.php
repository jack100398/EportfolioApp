<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingProgramModifiedRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_program_step_modified_records', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('action');
            $table->foreignId('program_user_id');
            $table->foreignId('program_unit_id');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('remarks');
            $table->foreignId('created_by')->nullable();
            $table->timestamps();

            $table->foreign('program_user_id', 'program_user_foreign')->references('id')->on('training_program_users');
            $table->foreign('program_unit_id', 'program_unit_foreign')->references('id')->on('training_program_units');
        });

        Schema::create('training_program_user_modified_records', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('action');
            $table->foreignId('training_program_id');
            $table->foreignId('user_id');
            $table->string('phone_number');
            $table->string('group_name');
            $table->foreignId('created_by')->nullable();
            $table->timestamps();

            $table->foreign('training_program_id', 'training_program_foreign')->references('id')->on('training_programs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_program_user_modified_records');
        Schema::dropIfExists('training_program_step_modified_records');
    }
}
