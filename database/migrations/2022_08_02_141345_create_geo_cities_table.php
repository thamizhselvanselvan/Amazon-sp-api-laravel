<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('geo_country_id')->nullable();
            $table->foreign('geo_country_id')->references('id')->on('geo_countries');
            $table->unsignedBigInteger('geo_state_id')->nullable();
            $table->foreign('geo_state_id')->references('id')->on('geo_states');
            $table->char('name', 60)->nullable();
            $table->char('latitude', 30)->nullable();
            $table->char('longitude', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_cities');
    }
};
