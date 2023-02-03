<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusIntoCatalogTable extends Migration
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
                $table->integer('status')->change()->default(0)->comment('0 = pending|new 1 = processed 2 = failed 5 = processing');
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
                $table->string('status', 5)->default(0)->change()->comment('');
            });
        }
    }
}
