<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialDownloadHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_download_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_material_id')->constrained('course_materials');
            $table->foreignId('student')->constrained('users');
            $table->integer('opened_counts');
            $table->integer('downloaded_counts');
            $table->time('reading_time');
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
        Schema::dropIfExists('material_download_histories');
    }
}
