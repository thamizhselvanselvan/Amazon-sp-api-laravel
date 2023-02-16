<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentStorePriceColumnInStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {

            $table->after("availability", function($table) {

                $table->string('current_store_price', 50)->default(0);
                $table->string('lowest_seller_id', 100)->default(0);
                $table->string('lowest_seller_price', 50)->default(0);
                $table->string('highest_seller_id', 100)->default(0);
                $table->string('highest_seller_price', 50)->default(0);
                $table->string('bb_winner_price', 50)->default(0);
                $table->string('bb_winner_id', 100)->default(0);
                $table->tinyInteger('is_bb_won')->default(0);
                
            });
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {

            $table->dropColumn('current_store_price');
            $table->dropColumn('lowest_seller_id');
            $table->dropColumn('lowest_seller_price');
            $table->dropColumn('highest_seller_id');
            $table->dropColumn('highest_seller_price');
            $table->dropColumn('bb_winner_price');
            $table->dropColumn('bb_winner_id');
            $table->dropColumn('is_bb_won');
            
        });
    }
}
