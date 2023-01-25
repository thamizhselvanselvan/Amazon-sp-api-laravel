<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePrefixFromBusinesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::connection('business')->rename('bc_catalog_business',  'catalog_business');
        //Schema::connection('business')->rename('bc_orders',  'orders');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::connection('business')->rename('catalog_business',  'bc_catalog_business');
        //Schema::connection('business')->rename('orders',  'bc_orders');
    }
}
