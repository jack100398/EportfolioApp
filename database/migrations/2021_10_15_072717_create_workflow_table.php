<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluatee')->constrained('users')->comment('受評者');
            $table->string('title')->comment('評核名稱');
            $table->foreignId('training_program_id')->nullable()->constrained('training_programs');
            $table->foreignId('form_id')->nullable()->constrained('form');
            $table->foreignId('unit_id')->nullable()->constrained('units')->comment('表單發送單位');
            $table->tinyInteger('type')->comment('門檻表單 : 1 、 課程表單 : 2 、 人工發送表單 : 3');
            $table->integer('data_id')->index()->comment('連接table id');
            $table->boolean('is_return')->default(false)->comment('是否有退件');
            $table->foreignId('create_by')->constrained('users');
            $table->date('start_at');
            $table->date('end_at');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('process', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflow');
            $table->foreignId('next_process_id')->nullable(true)->constrained('process');
            $table->tinyInteger('type')->comment('簽核方式');
            $table->tinyInteger('state')->default(0)->comment('處理狀態  0:未開始 1:開始 2:同意 3:不同意 4:轉呈 5:退回上一層');
            $table->text('opinion')->default('');
            $table->tinyInteger('error_status')->default(0)->comment('表單異常狀態: 0.無異常,1.沒有設定簽核流程角色,2.簽核流程人員已不存在(被刪除或未啟動)');
            $table->integer('role')->nullable(true)->comment('簽核角色');
            $table->boolean('is_default')->default(true)->comment('預設流程');
            $table->foreignId('sign_by')->nullable(true)->constrained('users')->comment('簽核者');
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
        Schema::dropIfExists('process');
        Schema::dropIfExists('workflow');
    }
}
