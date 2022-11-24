<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexIntoOrderOrderitemdetailsLabelAndOrderSellerCredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labels', function (Blueprint $table) {

            $table->index('awb_no', 'awb_no_index');
            $table->index('order_no', 'order_no_index');
        });

        Schema::connection('order_no_prefix')->table('orders', function (Blueprint $table) {

            $table->index('amazon_order_identifier', 'amazon_order_identifier_index');
        });

        Schema::connection('order_no_prefix')->table('orderitemdetails', function (Blueprint $table) {

            $table->index('amazon_order_identifier', 'amazon_order_identifier_index');
        });

        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {

            $table->index('seller_id', 'seller_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labels', function (Blueprint $table) {

            $table->dropIndex('awb_no_index');
            $table->dropIndex('order_no_index');
        });
        Schema::connection('order_no_prefix')->table('orders', function (Blueprint $table) {

            $table->dropIndex('amazon_order_identifier_index');
        });
        Schema::connection('order_no_prefix')->table('orderitemdetails', function (Blueprint $table) {

            $table->dropIndex('amazon_order_identifier_index');
        });
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {

            $table->dropIndex('seller_id_index');
        });
    }
}
