<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseandsourcetoShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('shipments', function (Blueprint $table) {
            $table->foreignId('warehouse')->after('id');
            $table->foreignId('source_id')->after('warehouse');
           
        });    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('shipments', function (Blueprint $table) {
        $table->dropColumn('warehouse');
        $table->dropColumn('source_id');
   
        
    });
           
    }
}
