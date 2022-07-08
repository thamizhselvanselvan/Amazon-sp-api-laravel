<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountrycodeAndSyncStatusInInventroyCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('catalogs', function (Blueprint $table) {

            $table->string('source')->after('id');
            $table->string('sync_status')->after('item_name');
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
            
            $table->dropColumn('source');
            $table->dropColumn('sync_status');
        });
    }
}
