<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreareShipntrackInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 100)->nullable();
            $table->string('awb_no', 100)->nullable();
            $table->string('mode', 100)->nullable();
            $table->string('invoice_date')->nullable();
            $table->string('sku')->nullable();
            $table->string('channel')->nullable();
            $table->string('shipped_by')->nullable();
            $table->string('arn_no')->nullable();
            $table->string('store_name')->nullable();
            $table->string('store_add')->nullable();
            $table->string('bill_to_name')->nullable();
            $table->string('bill_to_add')->nullable();
            $table->string('ship_to_name')->nullable();
            $table->string('ship_to_add')->nullable();
            $table->string('item_description')->nullable();
            $table->string('hsn_code')->nullable();
            $table->string('quantity')->nullable();
            $table->string('currency')->nullable();
            $table->string('product_price')->nullable();
            $table->string('taxable_value')->nullable();
            $table->string('total_including_taxes')->nullable();
            $table->string('grand_total')->nullable();
            $table->string('no_of_pcs')->nullable();
            $table->string('packing')->nullable();
            $table->string('dimension')->nullable();
            $table->string('actual_weight')->nullable();
            $table->string('charged_weight')->nullable();
            $table->string('sr_no')->nullable();
            $table->string('client_code')->nullable();
            $table->timestamps();

            $table->unique(['invoice_no','sku'], 'invoice_no_sku_unique');
            $table->index(['invoice_no'],'invoice_no_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('invoices');
    }
}
