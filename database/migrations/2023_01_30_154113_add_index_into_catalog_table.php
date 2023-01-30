<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexIntoCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('asin_destination_ins', function (Blueprint $table) {
            $table->index('updated_at', 'updated_at_index');
        });

        Schema::connection('catalog')->table('asin_destination_uss', function (Blueprint $table) {
            $table->index('updated_at', 'updated_at_index');
        });

        Schema::connection('catalog')->table('asin_destination_aes', function (Blueprint $table) {
            $table->index('updated_at', 'updated_at_index');
        });

        Schema::connection('catalog')->table('asin_destination_sas', function (Blueprint $table) {
            $table->index('updated_at', 'updated_at_index');
        });

        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {
            $table->index('updated_at', 'updated_at_index');
        });


        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {
            $table->index('updated_at', 'updated_at_index');
        });

        Schema::connection('catalog')->table('pricing_aes', function (Blueprint $table) {
            $table->index('updated_at', 'updated_at_index');
        });

        Schema::connection('catalog')->table('pricing_sas', function (Blueprint $table) {
            $table->index('updated_at', 'updated_at_index');
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
            $table->dropIndex('updated_at_index');
        });

        Schema::connection('catalog')->table('asin_destination_uss', function (Blueprint $table) {
            $table->dropIndex('updated_at_index');
        });

        Schema::connection('catalog')->table('asin_destination_aes', function (Blueprint $table) {
            $table->dropIndex('updated_at_index');
        });

        Schema::connection('catalog')->table('asin_destination_sas', function (Blueprint $table) {
            $table->dropIndex('updated_at_index');
        });

        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {
            $table->dropIndex('updated_at_index');
        });


        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {
            $table->dropIndex('updated_at_index');
        });

        Schema::connection('catalog')->table('pricing_aes', function (Blueprint $table) {
            $table->dropIndex('updated_at_index');
        });

        Schema::connection('catalog')->table('pricing_sas', function (Blueprint $table) {
            $table->dropIndex('updated_at_index');
        });
    }
}
