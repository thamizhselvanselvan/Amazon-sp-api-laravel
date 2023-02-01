<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBasePriceColumnToProductPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('buybox_stores')->table('product_push', function (Blueprint $table) {
            $table->string('base_price', 100)->after('push_price');
            $table->string('product_sku', 100)->after('id');
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
            $table->dropColumn('base_price');
            $table->dropColumn('product_sku');
        });
    }
}
