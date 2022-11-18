<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('amazon_order_id', 255)->nullable();
            $table->string('invoice_no', 255)->nullable();
            $table->string('mode', 255)->nullable();
            $table->string('bag_no', 50)->nullable();
            $table->string('invoice_date')->nullable();
            $table->string('sku', 255)->nullable();
            $table->string('channel', 255)->nullable();
            $table->string('shipped_by', 255)->nullable();
            $table->string('awb_no', 255)->nullable();
            $table->string('arn_no', 255)->nullable();
            $table->string('store_name', 255)->nullable();
            $table->string('store_add', 255)->nullable();
            $table->string('bill_to_name', 255)->nullable();
            $table->string('bill_to_add', 255)->nullable();
            $table->string('ship_to_name', 255)->nullable();
            $table->string('ship_to_add', 255)->nullable();
            $table->string('item_description', 255)->nullable();
            $table->string('hsn_code', 255)->nullable();
            $table->string('qty', 20)->nullable();
            $table->string('currency', 255)->nullable();
            $table->string('product_price', 50)->nullable();
            $table->string('taxable_value', 25)->nullable();
            $table->string('total_including_taxes', 25)->nullable();
            $table->string('grand_total', 50)->nullable();
            $table->string('no_of_pcs', 50)->nullable();
            $table->string('packing', 255)->nullable();
            $table->string('dimension', 255)->nullable();
            $table->string('actual_weight', 255)->nullable();
            $table->string('charged_weight', 255)->nullable();
            $table->string('sr_no', 50)->nullable();
            $table->string('client_code', 255)->nullable();
            $table->unique(['invoice_no', 'sku'], 'invoice_no_sku_unique');
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
        Schema::dropIfExists('invoices');
    }
}
