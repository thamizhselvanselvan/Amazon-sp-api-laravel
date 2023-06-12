<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InwardDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('shipntracking')->create('inwarding_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_ref_id')->nullable();;
            $table->foreign('master_ref_id')->references('id')->on('inwardings');
            $table->string('shipment_id')->nullable();
            $table->string('total_items_in_export')->nullable();
            $table->string('total_items_receved')->nullable();

            $table->unsignedBigInteger('mode')->nullable();;
            $table->foreign('mode')->references('id')->on('process_masters');


            $table->string('awb_number')->nullable();
            $table->string('international_awb_number')->comment('Forwarder 1 awb')->nullable();
            $table->string('purchase_tracking_id')->nullable();
            $table->string('order_id')->nullable();
            $table->tinyInteger('item_received_status')->comment('0-not received(no) 1-Received(yes)')->nullable();
            $table->tinyInteger('outward_status')->nullable()->default(0)->comment('0-not outwarded 1-outwarded')->nullable();
            $table->timestamps();

            $table->index(['awb_number', 'international_awb_number',], 'awb_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('shipntracking')->dropIfExists('inwarding_details');
    }
}
