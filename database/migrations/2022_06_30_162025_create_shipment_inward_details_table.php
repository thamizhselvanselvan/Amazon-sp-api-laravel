<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentInwardDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->create('shipment_inward_details', function (Blueprint $table) {
            $table->id();
            $table->string('ship_id');
            $table->foreignId('warehouse_id');
            $table->foreignId('source_id');
            $table->string('currency');
            $table->string('asin');
            $table->string('item_name');
            $table->string('price');
            $table->string('quantity');
            $table->string('out_quantity')->nullable();
            $table->string('balance_quantity')->nullable();
            $table->string('bin')->nullable();
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
        Schema::connection('inventory')->dropIfExists('shipment_inward_details');
    }
}
