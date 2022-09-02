<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexInCatalogOrderDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('asin_destination_ins', function (Blueprint $table) {
            $table->index('asin', 'asin_destination_ins_index');
        });
        Schema::connection('catalog')->table('asin_destination_uss', function (Blueprint $table) {
            $table->index('asin', 'asin_destination_uss_index');
        });

        Schema::connection('catalog')->table('asin_source_ins', function (Blueprint $table) {
            $table->index('asin', 'asin_source_ins_index');
        });
        Schema::connection('catalog')->table('asin_source_uss', function (Blueprint $table) {
            $table->index('asin', 'asin_source_uss_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('catalog')->table('asin_destination_ins', function (Blueprint $table) {
            $table->dropIndex('asin_destination_ins_index');
        });
        Schema::connection('catalog')->table('asin_destination_uss', function (Blueprint $table) {
            $table->dropIndex('asin_destination_uss_index');
        });

        Schema::connection('catalog')->table('asin_source_ins', function (Blueprint $table) {
            $table->dropIndex('asin_source_ins_index');
        });
        Schema::connection('catalog')->table('asin_source_uss', function (Blueprint $table) {
            $table->dropIndex('asin_source_uss_index');
        });
    }
}
