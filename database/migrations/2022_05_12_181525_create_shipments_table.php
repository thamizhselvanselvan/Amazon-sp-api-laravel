<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('source_id');
            $table->string('ship_id');
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
        Schema::connection('inventory')->dropIfExists('shipments');
    }
}
