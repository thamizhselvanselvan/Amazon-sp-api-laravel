<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseToShipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::connection('inventory')->table('shipments', function (Blueprint $table) {
        $table->string('warehouse')->nullable()->after('id');
        $table->string('country')->nullable()->after('warehouse');
        $table->string('currency')->nullable()->after('country');
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
            $table->dropColumn('warehouse');
            $table->dropColumn('country');
            $table->dropColumn('currency');
           
    
        });
    }
}
