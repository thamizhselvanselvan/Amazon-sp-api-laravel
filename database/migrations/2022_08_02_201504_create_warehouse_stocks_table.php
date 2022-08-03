<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('Warehouse');
            $table->string('opeaning_stock');
            $table->string('opeaning_amount');
            $table->string('inwarding');
            $table->string('inw_amount');
            $table->string('outwarding');
            $table->string('outw_amount');
            $table->string('closing_stock');
            $table->string('closing_amount');
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
        Schema::connection('inventory')->dropIfExists('warehouse_stocks');
    }
}
