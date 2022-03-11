<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOccupationalClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('occupational_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('occupational_classes');
            $table->timestamps();
            $table->softDeletes();
        });

        // 補上訓練計畫外鍵
        DB::statement('ALTER TABLE `training_programs`
            ADD CONSTRAINT `FK_occupational_classes` FOREIGN KEY (`occupational_class_id`)
            REFERENCES `occupational_classes` (`id`)
            ON UPDATE RESTRICT ON DELETE RESTRICT
        ;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `training_programs`
            DROP FOREIGN KEY `FK_occupational_classes`;
        ');
        Schema::dropIfExists('occupational_classes');
    }
}
