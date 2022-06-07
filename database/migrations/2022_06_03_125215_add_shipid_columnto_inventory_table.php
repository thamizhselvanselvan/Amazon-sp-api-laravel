<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipidColumntoInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->table('inventory', function (Blueprint $table) {
            $table->dropColumn('warehouse_id');
            $table->string('ship_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->table('inventory', function (Blueprint $table) {
            $table->foreignId('warehouse_id');
            $table->dropColumn('ship_id');
        });
    }
}
