<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExportColumnIntoDestinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['asin_destination_aes', 'asin_destination_ins', 'asin_destination_sas', 'asin_destination_uss'];
        foreach ($tables as $table) {
            Schema::connection('catalog')->table($table, function (Blueprint $table) {
                $table->integer('export')->nullable()->default(0)->after('priority')->comment('0 = pending 1 = processed 5 = processing');
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
        $tables = ['asin_destination_aes', 'asin_destination_ins', 'asin_destination_sas', 'asin_destination_uss'];
        foreach ($tables as $table) {
            Schema::connection('catalog')->table($table, function (Blueprint $table) {
                $table->dropColumn('export');
            });
        }
    }
}
