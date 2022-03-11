<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentTypeFactory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type')->comment(
                '4:口頭測驗,5:筆試測驗,6:實地操作,7:書面報告,
                8:其他,9:DOPS,10:mini-CEX,11:CbD,12:紀錄單,
                13:心得報告,14:附件上傳,15:出席參與,16:線上測驗,18:實例討論,19:實證醫學病例及期刊討論紀錄單
                ,20:輔助教材,21:滿意度問卷,22:學員考核表,23:其他表單,24:指定紀錄單,25:直接通過'
            );
            $table->string('assessment_name');
            $table->foreignId('unit_id')->nullable();
            $table->foreignId('source')->nullable()->constrained('form');
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
        Schema::dropIfExists('assessment_types');
    }
}
