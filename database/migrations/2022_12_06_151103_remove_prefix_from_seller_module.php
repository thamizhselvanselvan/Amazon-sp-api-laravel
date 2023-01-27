<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePrefixFromSellerModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //Schema::connection('seller')->rename('se_asin_master_sellers',  'asin_master_sellers');
        //Schema::connection('seller')->rename('se_seller_asin_details',  'seller_asin_details');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        //Schema::connection('seller')->rename('asin_master_sellers',  'se_asin_master_sellers');
        //Schema::connection('seller')->rename('seller_asin_details',  'se_seller_asin_details');

    }
}
