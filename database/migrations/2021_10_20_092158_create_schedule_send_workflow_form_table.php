<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleSendWorkflowFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_send_workflow_form', function (Blueprint $table) {
            $table->id();
            $table->integer('key_id');
            $table->string('title');
            $table->foreignId('unit_id')->constrained('units');
            $table->tinyInteger('type');
            $table->date('start_at');
            $table->date('end_at');
            $table->integer('create_at');
            $table->foreignId('student_id')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_send_workflow_form');
    }
}
