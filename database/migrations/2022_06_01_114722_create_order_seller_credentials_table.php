<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSellerCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->create('order_seller_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('seller_id')->unique();
            $table->string('mws_region_id');
            $table->string('store_name');
            $table->string('merchant_id');
            $table->string('auth_code', 1000);
            $table->string('dump_order')->default(0);
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
        Schema::connection('order')->dropIfExists('order_seller_credentials');
    }
}
