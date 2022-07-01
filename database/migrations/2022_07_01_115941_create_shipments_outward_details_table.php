<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsOutwardDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->create('shipments_outward_details', function (Blueprint $table) {
            $table->id();
            $table->string('ship_id');
            $table->foreignId('warehouse_id');
            $table->foreignId('destination_id');
            $table->string('currency');
            $table->string('asin');
            $table->string('item_name');
            $table->string('price');
            $table->string('quantity');
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
        Schema::connection('inventory')->dropIfExists('shipments_outward_details');
    }
}
