<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerAsinPricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('seller')->create('seller_asin_details', function (Blueprint $table) {
            $table->id();
            $table->string('seller_id');
            $table->string('asin');
            $table->string('is_fulfilment_by_amazon')->nullable();
            $table->string('price')->nullable();
            $table->string('status')->nullable()->default('0');
            $table->unique(['seller_id','asin'], 'seller_id_asin_unique');
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
        Schema::connection('seller')->dropIfExists('seller_asin_details');
    }
}
