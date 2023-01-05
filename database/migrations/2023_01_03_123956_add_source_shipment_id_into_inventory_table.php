<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceShipmentIdIntoInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = $this->TablesNameArray();
        foreach ($tables as $table_name => $after) {
            Schema::connection('inventory')->table($table_name, function (Blueprint $table) use ($after) {
                $table->string('ss_id', 100)->nullable()->after($after)->comment('Source Shipment Id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = $this->TablesNameArray();
        foreach ($tables as $table_name => $after) {
            Schema::connection('inventory')->table($table_name, function (Blueprint $table) {
                $table->dropColumn('ss_id');
            });
        }
    }

    public function TablesNameArray()
    {
        $tables = [
            'inventory' => 'source_id',
            'shipments_inward' => 'source_id',
            'shipment_inward_details' => 'source_id'
        ];
        return $tables;
    }
}
