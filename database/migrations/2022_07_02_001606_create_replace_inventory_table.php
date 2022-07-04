<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplaceInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::connection('inventory')->create('inventory', function (Blueprint $table) {
                $table->id();
                $table->string('ship_id');
                $table->foreignId('warehouse_id');
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
            Schema::connection('inventory')->dropIfExists('inventory');
        }
}
