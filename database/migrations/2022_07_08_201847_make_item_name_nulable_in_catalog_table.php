<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeItemNameNulableInCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('catalogs', function (Blueprint $table) {
            $table->string('item_name')->nullable()->change();
            $table->string('sync_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('catalogs', function (Blueprint $table) {
            $table->string('item_name')->nullable(false)->change();
            $table->string('sync_status')->nullable(false)->change();
        });
    }
}
