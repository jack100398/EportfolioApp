<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_form', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('評核名稱');
            $table->foreignId('training_program_id')->constrained('training_programs');
            $table->foreignId('default_workflow_id')->nullable()->constrained('default_workflow');
            $table->foreignId('form_id')->constrained('form');
            $table->integer('send_amount')->comment('表單數');
            $table->integer('form_start_at')->comment('表單發送日期(評核日期)');
            $table->integer('form_write_at')->comment('表單填寫天數');
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
        Schema::dropIfExists('manual_form');
    }
}
