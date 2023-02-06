<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexIntoAsinSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['asin_source_aes', 'asin_source_ins', 'asin_source_sas', 'asin_source_uss'];
        foreach ($tables as $table) {
            Schema::connection('catalog')->table($table, function (Blueprint $table) {
                $table->index('status', 'status_index');
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
        $tables = ['asin_source_aes', 'asin_source_ins', 'asin_source_sas', 'asin_source_uss'];
        foreach ($tables as $table) {
            Schema::connection('catalog')->table($table, function (Blueprint $table) {
                $table->dropIndex('status', 'status_index');
            });
        }
    }
}
