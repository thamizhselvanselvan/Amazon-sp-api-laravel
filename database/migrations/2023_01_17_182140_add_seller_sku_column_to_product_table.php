<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellerSkuColumnToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('products', function (Blueprint $table) {
         $table->string('product_sku')->after('asin');
           
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
            $table->dropColumn('product_sku');
        });
    }
}
