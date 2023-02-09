<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SplitStoresCountryWise extends Migration
{
    private $table_name = ['products_ins', 'products_aes', 'products_sas'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->table_name as $name) {

            Schema::connection('buybox_stores')->create($name, function (Blueprint $table) {
                $table->id();
                $table->string('store_id', 10);
                $table->string('asin', 60);
                $table->string('product_sku');
                $table->tinyInteger('cyclic')->default('0')->comment("0 = pending|new, 1 = processed, 5 = processing ");
                $table->string('bb_cyclic')->nullable()->default(0)->comment("0 = pending, 1 = Processed, 5 = processing");
                $table->tinyInteger('priority')->nullable();
                $table->tinyInteger('availability')->default(0);
                $table->string('latency')->default(7);
                $table->string('base_price', 30)->nullable();
                $table->string('ceil_price', 30)->nullable();
                $table->string('app_360_price', 30)->nullable();
                $table->string('bb_price', 30)->nullable();
                $table->string('push_price', 30)->nullable();
                $table->string('store_price', 30)->nullable();
                $table->string('cyclic_push')->default('0')->comment("0 = pending|new, 1 = processed, 5 = processing ");;
                $table->string('lowest_seller_id')->nullable();
                $table->string('lowest_seller_price')->nullable();
                $table->string('highest_seller_id')->nullable();
                $table->string('highest_seller_price')->nullable();
                $table->string('bb_winner_id')->nullable();
                $table->string('bb_winner_price')->nullable();
                $table->string('is_bb_own')->nullable()->default(0)->comment("0 = lost, 1 = won");

                $table->timestamps();

                $table->index('asin', 'asin_index');
                $table->index('priority', 'priority_index');
                $table->index('availability', 'availability_index');
                $table->index('updated_at', 'updated_at_index');
             
                $table->unique(["asin", "store_id"], 'asin_store_id_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->table_name as $name) {
            Schema::connection('buybox_stores')->dropIfExists($name);
        }
    }
}
