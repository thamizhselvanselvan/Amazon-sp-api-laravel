<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRateMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_masters', function (Blueprint $table) {
            $table->id();
            $table->string('weight', 50)->nullable()->comment('kg');
            $table->string('base_rate', 50)->nullable();
            $table->string('commission', 50)->nullable();
            $table->string('lmd_cost', 50)->nullable();
            $table->string('source_destination', 50)->nullable();
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
        Schema::dropIfExists('rate_masters');
    }
}
