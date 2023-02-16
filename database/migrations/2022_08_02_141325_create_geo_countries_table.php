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
        Schema::create('geo_countries', function (Blueprint $table) {
            $table->id();
            $table->char('code', 3)->nullable();
            $table->char('iso3_code', 6)->nullable();
            $table->char('name', 60)->nullable();
            $table->char('region', 20)->nullable();
            $table->char('currency_code', 6)->nullable();
            $table->char('currency_symbol', 10)->nullable();
            $table->char('currency_name', 40)->nullable();
            $table->char('mobile_prefix', 16)->nullable();
            $table->char('emoji_code', 20)->nullable();
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
        Schema::dropIfExists('geo_countries');
    }
};
