<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerAsinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('seller')->create('asin_master_seller', function (Blueprint $table) {
            $table->id();
            $table->string('seller_id');
            $table->string('asin');
            $table->string('source')->nullable();
            $table->string('destination_1')->nullable();
            $table->string('destination_2')->nullable();
            $table->string('destination_3')->nullable();
            $table->string('destination_4')->nullable();
            $table->string('destination_5')->nullable();
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
        Schema::dropIfExists('asin_master_seller');
    }
}
