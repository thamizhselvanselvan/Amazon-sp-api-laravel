<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsSoldByAmazonIntoPriceDestinationTable extends Migration
{
    public $pricing_tables = [
        'pricing_ins',
        'pricing_uss',
        'pricing_aes',
        'pricing_sas'
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->pricing_tables as $pricing_table) {

            Schema::connection('catalog')->table($pricing_table, function (Blueprint $table) {

                $table->tinyInteger('is_sold_by_amazon')->default(0)->after('available');
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
        foreach ($this->pricing_tables as $pricing_table) {

            Schema::connection('catalog')->table($pricing_table, function (Blueprint $table) {
                $table->dropColumn('is_sold_by_amazon');
            });
        }
    }
}
