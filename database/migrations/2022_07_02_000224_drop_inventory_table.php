<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->dropIfExists('inventory');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->create('inventory', function (Blueprint $table) {
            $table->id();
            //$table->foreignId('warehouse_id');
            $table->string('asin');
            $table->string('item_name');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('bin');
            $table->integer('ship_id');
            $table->timestamps();
        });
    }
}
