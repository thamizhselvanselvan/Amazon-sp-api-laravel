<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePrefixFromOrderDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::connection('order')->rename('ord_order_seller_credentials', 'order_seller_credentials');
        //Schema::connection('order')->rename('ord_order_update_details', 'order_update_details');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::connection('order')->rename('order_seller_credentials', 'ord_order_seller_credentials');
        //Schema::connection('order')->rename('order_update_details', 'ord_order_update_details');
    }
}
