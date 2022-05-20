<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCountryFromShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::connection('inventory')->table('shipments', function (Blueprint $table) {
                $table->dropColumn('country');
                $table->dropColumn('warehouse');
                $table->dropColumn('source_id');
           
                
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('shipments', function (Blueprint $table) {
            $table->string('country');
            $table->string('warehouse');
            $table->string('source_id');
           
        });
    }
}
