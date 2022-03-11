<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormWriteRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_write_record', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflow');
            $table->foreignId('user_id')->constrained('users');
            $table->json('result');
            $table->bigInteger('flag')->comment('項目分類  1.暫存資料 2.填寫結果');
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
        Schema::dropIfExists('form_write_record');
    }
}
