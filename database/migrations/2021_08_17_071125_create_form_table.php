<?php

use App\Services\Form\Enum\IsSharableEnum;
use App\Services\Form\Enum\ReviewedEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_form_id')->nullable(true)->constrained('form')->comment('舊表單編號');
            $table->string('name')->comment('表單名稱');
            $table->integer('type')->comment('表單種類');
            $table->integer('version')->comment('表單版本號')->default(1);
            $table->json('is_writable')->comment('1:開放學員自行建立簽核表單 2:開放教師自行建立簽核表單')->nullable(true);
            $table->tinyInteger('reviewed')->comment('是否審核通過 0:未審核 1:通過 2:拒絕 3:編輯中，先不要讓單位管理者審核')->default(ReviewedEnum::PASS);
            $table->json('questions')->comment('表單題目列表');
            $table->boolean('is_enabled')->comment('是否啟用 0:停用 1:啟用')->default(true);
            $table->json('form_default_workflow')->comment('評核表單預設簽核(用json存多個簽核流程)');
            $table->integer('course_form_default_assessment')->comment('課程表單預設簽核(只能選擇單一一個)');
            $table->tinyInteger('is_sharable')->comment('是否表單共用')->default(IsSharableEnum::NONE);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('form_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('form');
            $table->foreignId('unit_id')->constrained('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_unit');
        Schema::dropIfExists('form');
    }
}
