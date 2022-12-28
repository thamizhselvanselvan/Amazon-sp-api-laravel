<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInwardDateProcurementDateColumnIntoInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $columns_name = [
            'string' => 'procurement_price',
            'timestamp' => 'inwarded_at'
        ];

        $this->createColums(
            'inventory',
            $columns_name,
            [
                'procurement_price' => 'price',
                'inwarded_at' => 'bin'
            ]
        );

        $this->createColums(
            'shipment_inward_details',
            $columns_name,
            [
                'procurement_price' => 'price',
                'inwarded_at' => 'quantity'
            ]
        );

        $this->createColums('shipments_inward', ['timestamp' => 'inwarded_at'], ['inwarded_at' => 'shipment_count']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $columns_name = [
            'inventory' => ['procurement_price', 'inwarded_at'],
            'shipment_inward_details' => ['procurement_price', 'inwarded_at'],
            'shipments_inward' => ['inwarded_at']
        ];

        $this->dropcolumns($columns_name);
    }

    public function createColums($table_name, $columns_name, $after = null)
    {
        Schema::connection('inventory')->table($table_name, function (Blueprint $table) use ($columns_name, $after) {
            foreach ($columns_name as $key => $column) {
                $table->$key($column)->nullable()->after($after[$column]);
            }
        });
    }

    public function dropcolumns($table_name)
    {
        foreach ($table_name as $key => $columns_name) {
            Schema::connection('inventory')->table($key, function (Blueprint $table) use ($columns_name) {
                foreach ($columns_name as $column) {
                    $table->dropColumn($column);
                }
            });
        }
    }
}
