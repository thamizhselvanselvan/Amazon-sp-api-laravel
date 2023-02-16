<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('our_seller_identifier')->nullable();
            $table->string('country', 191)->nullable();
            $table->string('source', 191)->nullable();
            $table->string('amazon_order_identifier', 191)->nullable();
            $table->string('purchase_date', 191)->nullable();
            $table->string('last_update_date', 191)->nullable();
            $table->string('order_status', 191)->nullable();
            $table->string('fulfillment_channel', 191)->nullable();
            $table->string('sales_channel', 191)->nullable();
            $table->string('ship_service_level', 191)->nullable();
            $table->string('order_total', 191)->nullable();
            $table->unsignedInteger('number_of_items_shipped')->nullable();
            $table->unsignedInteger('number_of_items_unshipped')->nullable();
            $table->string('payment_method', 191)->nullable();
            $table->string('payment_method_details', 191)->nullable();
            $table->string('marketplace_identifier', 191)->nullable();
            $table->string('shipment_service_level_category', 191)->nullable();
            $table->string('order_type', 191)->nullable();
            $table->string('earliest_ship_date', 191)->nullable();
            $table->string('latest_ship_date', 191)->nullable();
            $table->string('earliest_delivery_date', 191)->nullable();
            $table->string('latest_delivery_date', 191)->nullable();
            $table->string('is_business_order', 191)->nullable();
            $table->string('is_prime', 191)->nullable();
            $table->string('is_premium_order', 191)->nullable();
            $table->string('is_global_express_enabled', 191)->nullable();
            $table->string('is_replacement_order', 191)->nullable();
            $table->string('is_sold_by_ab', 191)->nullable();
            $table->string('default_ship_from_location_address')->nullable();
            $table->string('is_ispu', 191)->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('buyer_info', 191)->nullable();
            $table->string('automated_shipping_settings', 191)->nullable();
            $table->unsignedTinyInteger('order_item')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('seller_order_identifier', 191)->nullable();
            $table->string('is_access_point_order', 191)->nullable();
            $table->string('has_regulated_items', 191)->nullable();
            $table->string('easy_ship_shipment_status', 191)->nullable();
            $table->string('payment_execution_detail', 191)->nullable();
            $table->string('replaced_order_identifier', 191)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->dropIfExists('orders');
    }
};
