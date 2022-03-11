<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNominalRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nominal_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('角色名稱');
            $table->integer('type')->comment('該角色綁定的類別（如計畫／計畫單位等）');
            $table->boolean('is_active')->comment('是否啟用');
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
        Schema::dropIfExists('nominal_roles');
    }
}
