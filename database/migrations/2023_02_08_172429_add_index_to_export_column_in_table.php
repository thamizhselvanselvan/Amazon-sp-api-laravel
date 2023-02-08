<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToExportColumnInTable extends Migration
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
                $table->index('export', 'export_index');
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
                $table->dropIndex('export_index');
            });
        }
    }
}
