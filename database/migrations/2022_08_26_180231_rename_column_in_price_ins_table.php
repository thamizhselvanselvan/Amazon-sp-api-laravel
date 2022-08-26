<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnInPriceInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalog')->table('pricing_ins', function (Blueprint $table) {

            $table->renameColumn('avaliable', 'available');
        });
        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {
            $table->renameColumn('avaliable', 'available');
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
            $table->renameColumn('available', 'avaliable');
        });

        Schema::connection('catalog')->table('pricing_uss', function (Blueprint $table) {
            $table->renameColumn('available', 'avaliable');
        });
    }
}
