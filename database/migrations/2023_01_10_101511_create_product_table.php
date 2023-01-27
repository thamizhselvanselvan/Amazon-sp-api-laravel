<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->create('products', function (Blueprint $table) {
            $table->id();
            $table->string('store_id',10);
            $table->string('asin', 60);
            $table->tinyInteger('cyclic')->default('0')->comment("0 = pending|new, 1 = processed, 5 = processing ");
            $table->tinyInteger('priority')->default(0);
            $table->tinyInteger('availability')->default(0);
            $table->string('latency',25)->nullable();
            $table->string('base_price',30)->nullable();
            $table->string('ceil_price',30)->nullable();
            $table->string('app_360_price',30)->nullable();
            $table->string('bb_price',30)->nullable();
            $table->string('push_price',30)->nullable();
            $table->string('store_price',30)->nullable();
          
            $table->timestamps();

            $table->index('asin','asin_index');
            $table->index('priority', 'priority_index');
            $table->index('availability', 'availability_index');

            $table->unique(["asin", "store_id"], 'asin_store_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('buybox_stores')->dropIfExists('products');
    }
}
