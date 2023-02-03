<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnIntoPricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['pricing_aes', 'pricing_ins', 'pricing_sas', 'pricing_uss'];
        foreach ($tables as $table) {

            Schema::connection('catalog')->table($table, function (Blueprint $table) {
                $table->integer('status')->after('asin')->default(0)->comment('0 = calc once 1 = re-calc');
                $table->string('volumetric_weight', 100)->after('weight')->nullable();
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
        $tables = ['pricing_aes', 'pricing_ins', 'pricing_sas', 'pricing_uss'];
        foreach ($tables as $table) {

            Schema::connection('catalog')->table($table, function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('volumetric_weight');
            });
        }
    }
}
