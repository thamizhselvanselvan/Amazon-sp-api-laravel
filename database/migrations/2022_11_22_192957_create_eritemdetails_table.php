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
        Schema::connection('order')->create('orderitemdetails', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('seller_identifier')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->string('country', 191)->nullable();
            $table->string('source', 191)->nullable();
            $table->string('amazon_order_identifier', 191)->nullable();
            $table->string('order_item_identifier', 191)->nullable();
            $table->string('asin', 191)->nullable();
            $table->string('seller_sku', 191)->nullable();
            $table->text('title')->nullable();
            $table->unsignedInteger('quantity_ordered')->nullable();
            $table->unsignedInteger('quantity_shipped')->nullable();
            $table->string('product_info', 191)->nullable();
            $table->unsignedTinyInteger('points_granted')->nullable();
            $table->string('item_price', 191)->nullable();
            $table->string('shipping_price', 191)->nullable();
            $table->string('item_tax', 191)->nullable();
            $table->string('shipping_tax', 191)->nullable();
            $table->string('shipping_discount', 191)->nullable();
            $table->string('shipping_discount_tax', 191)->nullable();
            $table->string('promotion_discount', 191)->nullable();
            $table->string('promotion_discount_tax', 191)->nullable();
            $table->text('promotion_identifiers')->nullable();
            $table->string('cod_fee', 191)->nullable();
            $table->string('cod_fee_discount', 191)->nullable();
            $table->unsignedTinyInteger('is_gift')->nullable();
            $table->string('condition_note', 191)->nullable();
            $table->string('condition_identifier', 191)->nullable();
            $table->string('condition_subtype_identifier', 191)->nullable();
            $table->unsignedTinyInteger('scheduled_delivery_start_date')->nullable();
            $table->unsignedTinyInteger('scheduled_delivery_end_date')->nullable();
            $table->string('price_designation', 191)->nullable();
            $table->unsignedTinyInteger('tax_collection')->nullable();
            $table->unsignedTinyInteger('serial_number_required')->nullable();
            $table->unsignedTinyInteger('is_transparency')->nullable();
            $table->unsignedTinyInteger('ioss_number')->nullable();
            $table->unsignedTinyInteger('store_chain_store_identifier')->nullable();
            $table->unsignedTinyInteger('deemed_reseller_category')->nullable();
            $table->string('buyer_info', 191)->nullable();
            $table->text('shipping_address')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('buyer_requested_cancel', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->dropIfExists('orderitemdetails');
    }
};
