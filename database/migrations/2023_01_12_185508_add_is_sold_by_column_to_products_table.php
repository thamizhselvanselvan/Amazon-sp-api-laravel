<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSoldByColumnToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('products', function (Blueprint $table) {
          
            $table->string('lowest_seller_id')->nullable()->after('store_price');
            $table->string('lowest_seller_price')->nullable()->after('lowest_seller_id');
            $table->string('highest_seller_id')->nullable()->after('lowest_seller_price');
            $table->string('highest_seller_price')->nullable()->after('highest_seller_id');
            $table->string('bb_winner_id')->nullable()->after('highest_seller_price');
            $table->string('bb_winner_price')->nullable()->after('bb_winner_id');
            $table->string('is_bb_own')->nullable()->after('bb_winner_price')->default(0)->comment("0 = lost, 1 = won");;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('buybox_stores')->table('products', function (Blueprint $table) {

            $table->dropColumn('lowest_seller_id');
            $table->dropColumn('lowest_seller_price');
            $table->dropColumn('highest_seller_id');
            $table->dropColumn('highest_seller_price');
            $table->dropColumn('bb_winner_id');
            $table->dropColumn('bb_winner_price');
            $table->dropColumn ('is_bb_own');
        });
    }
}
