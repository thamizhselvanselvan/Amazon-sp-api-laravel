<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePrefixFromInventoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventory')->rename('in_bins', 'bins');
        Schema::connection('inventory')->rename('in_catalogs',  'catalogs');
        Schema::connection('inventory')->rename('in_cities',   'cities');
        Schema::connection('inventory')->rename('in_countries', 'countries');
        Schema::connection('inventory')->rename('in_disposes', 'disposes');
        Schema::connection('inventory')->rename('in_inventory', 'inventory');
        Schema::connection('inventory')->rename('in_racks', 'racks');
        Schema::connection('inventory')->rename('in_shelves',    'shelves');
        Schema::connection('inventory')->rename('in_shipments_inward',    'shipments_inward');
        Schema::connection('inventory')->rename('in_shipments_outward',    'shipments_outward');
        Schema::connection('inventory')->rename('in_shipments_outward_details',    'shipments_outward_details');
        Schema::connection('inventory')->rename('in_shipment_inward_details',    'shipment_inward_details');
        Schema::connection('inventory')->rename('in_states',    'states');
        Schema::connection('inventory')->rename('in_stocks',    'stocks');
        Schema::connection('inventory')->rename('in_tags',    'tags');
        Schema::connection('inventory')->rename('in_tag_stocks',    'tag_stocks');
        Schema::connection('inventory')->rename('in_vendors',    'vendors');
        Schema::connection('inventory')->rename('in_warehouses',    'warehouses');
        Schema::connection('inventory')->rename('in_warehouse_stocks', 'warehouse_stocks');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('inventory')->rename('bins',  'in_bins');
        Schema::connection('inventory')->rename('catalogs',  'in_catalogs');
        Schema::connection('inventory')->rename('cities',  'in_cities');
        Schema::connection('inventory')->rename('countries',  'in_countries');
        Schema::connection('inventory')->rename('disposes',  'in_disposes');
        Schema::connection('inventory')->rename('inventory',  'in_inventory');
        Schema::connection('inventory')->rename('racks',  'in_racks');
        Schema::connection('inventory')->rename('shelves',  'in_shelves');
        Schema::connection('inventory')->rename('shipments_inward',  'in_shipments_inward');
        Schema::connection('inventory')->rename('shipments_outward',  'in_shipments_outward');
        Schema::connection('inventory')->rename('shipments_outward_details',  'in_shipments_outward_details');
        Schema::connection('inventory')->rename('shipment_inward_details',  'in_shipment_inward_details');
        Schema::connection('inventory')->rename('states',  'in_states');
        Schema::connection('inventory')->rename('stocks',  'in_stocks');
        Schema::connection('inventory')->rename('tags',  'in_tags');
        Schema::connection('inventory')->rename('tag_stocks',  'in_tag_stocks');
        Schema::connection('inventory')->rename('vendors',  'in_vendors');
        Schema::connection('inventory')->rename('warehouses',  'in_warehouses');
        Schema::connection('inventory')->rename('warehouse_stocks',  'in_warehouse_stocks');
    }
}
