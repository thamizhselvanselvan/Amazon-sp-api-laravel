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
        Schema::create('geo_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('geo_country_id')->nullable();
            $table->foreign('geo_country_id')->references('id')->on('geo_countries');
            $table->char('code', 6)->nullable();
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
        Schema::dropIfExists('geo_states');
    }
};
