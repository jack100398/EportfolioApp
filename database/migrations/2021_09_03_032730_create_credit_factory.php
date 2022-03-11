<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditFactory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('year')->default(0);
            $table->unsignedBigInteger('sort')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('credits');
            $table->string('credit_name');
            $table->unsignedBigInteger('credit_type')->comment('1:院內教育學分  , 2:繼續教育學分');
            $table->json('training_time')->nullable()->comment('806 各職類需教育時數 格式=>[職類id:時數,職類id:時數]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credits');
    }
}
