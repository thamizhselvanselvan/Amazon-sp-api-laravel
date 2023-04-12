<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipntrackLabelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 255)->index('order_no_index');
            $table->string('order_item_id', 255)->nullable();
            $table->string('bag_no', 191)->nullable();
            $table->string('forwarder', 50)->nullable();
            $table->string('awb_no', 255)->nullable()->index('awb_no_index');
            $table->date('order_date')->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('county', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('product_name', 300)->nullable();
            $table->string('sku', 191)->nullable();
            $table->integer('quantity');
            $table->unique(['order_no', 'order_item_id', 'bag_no'], 'order_item_bag_unique');
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
        Schema::connection('shipntracking')->dropIfExists('labels');
    }
}
