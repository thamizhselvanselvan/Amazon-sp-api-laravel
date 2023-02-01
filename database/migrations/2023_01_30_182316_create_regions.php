<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->integer('currency_id');
            $table->string('region', 255)->nullable();
            $table->string('region_code', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('site_url', 255)->nullable();
            $table->string('marketplace_id', 255)->nullable();
            $table->integer('status')->nullable()->default(0)->comment('0 = inactive, 1 = active');
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
        Schema::dropIfExists('regions');
    }
}
