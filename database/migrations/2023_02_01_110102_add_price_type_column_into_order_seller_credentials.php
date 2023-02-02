<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceTypeColumnIntoOrderSellerCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
            $table->string('price_calculation_type', 100)->nullable()->after('destination');
            $table->tinyInteger('price_calculation_value')->nullable()->after('price_calculation_type')->comment('percentage|fixed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('order')->table('order_seller_credentials', function (Blueprint $table) {
            $table->dropColumn('price_calculation_type');
            $table->dropColumn('price_calculation_value');
        });
    }
}
