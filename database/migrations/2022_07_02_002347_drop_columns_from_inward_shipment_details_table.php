<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromInwardShipmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('shipment_inward_details', function (Blueprint $table) {
            $table->dropColumn('out_quantity');
            $table->dropColumn('balance_quantity');
            $table->dropColumn('bin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('shipment_inward_details', function (Blueprint $table) {

            $table->string('out_quantity')->nullable();
            $table->string('balance_quantity')->nullable();
            $table->string('bin')->nullable();
        });
    }
}
