<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingProgramCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_program_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('training_program_categories');
            $table->foreignId('training_program_id')
                ->constrained('training_programs')->comment('該分類所屬計畫');
            $table->foreignId('unit_id')->constrained('units')->comment('該分類所屬單位');
            $table->foreignId('default_category_id')->nullable()->constrained('default_categories')
                ->comment('對應的預設架構ID，作為跟預設架構同步使用');
            $table->boolean('is_training_item')->comment('是否為訓練項目');
            $table->string('name', 512);
            $table->integer('sort')->comment('顯示排序');
            $table->foreignId('created_by');
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE `threshold_form`
            ADD CONSTRAINT `FK_program_category` FOREIGN KEY (`program_category_id`)
            REFERENCES `training_program_categories` (`id`)
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
        DB::statement('ALTER TABLE `threshold_form`
            DROP FOREIGN KEY `FK_program_category`;
        ');
        Schema::dropIfExists('training_program_categories');
    }
}
