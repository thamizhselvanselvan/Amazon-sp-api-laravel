<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCountryCodeInOrderSellerCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_seller_credentials', function (Blueprint $table) {
            
            $table->string('country_code')->after('mws_region_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_seller_credentials', function (Blueprint $table) {
            
            $table->dropColumn('country_code');
        });
    }
}
