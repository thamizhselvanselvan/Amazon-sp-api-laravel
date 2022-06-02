<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromSellerOrderCredentialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
           
            $table->dropColumn('mws_region_id');
            $table->dropColumn('merchant_id');
            $table->dropColumn('auth_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {

            $table->string('mws_region_id')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('auth_code', 1000)->nullable();
        });
    }
}
