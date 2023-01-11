<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsSoldByAmazonIntoPriceDestinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {

            $table->string('is_sold_by_amazon', 10)->after('available')->default('0');
        });

        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {

            $table->string('is_sold_by_amazon', 10)->after('available')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {
            $table->dropColumn('is_sold_by_amazon');
        });

        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {
            $table->dropColumn('is_sold_by_amazon');
        });
    }
}
