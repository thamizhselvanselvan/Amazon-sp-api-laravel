<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->create('order_update_details', function (Blueprint $table) {
            $table->id();
            $table->string('store_id');
            $table->string('amazon_order_id');
            $table->string('order_item_id');
            $table->string('courier_name')->nullable();
            $table->string('courier_awb')->nullable();
            $table->string('zoho_id')->nullable();
            $table->string('zoho_order_id')->nullable();
            $table->string('amzn_temp_order_status')->nullable();
            $table->timestamps();
            $table->unique('order_item_id', 'amzn_ord_item_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->dropIfExists('order_update_details');
    }
}
