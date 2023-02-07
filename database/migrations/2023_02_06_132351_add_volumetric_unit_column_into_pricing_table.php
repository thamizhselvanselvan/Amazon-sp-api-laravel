<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVolumetricUnitColumnIntoPricingTable extends Migration
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
                $table->renameColumn('volumetric_weight', 'volumetric_weight_pounds');
                $table->string('volumetric_weight_kg', 100)->after('volumetric_weight')->nullable();
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
                $table->renameColumn('volumetric_weight_pounds', 'volumetric_weight');
                $table->dropColumn('volumetric_weight_kg');
            });
        }
    }
}
