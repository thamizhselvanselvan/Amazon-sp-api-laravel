<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class DBbackupController extends Controller
{
    public function index()
    {
        $ignoreArray = [
            'order_no_prefix',
            'buybox',
            'bbstores',
            'aws',
            'b2cship',
            'mongodb',
            'cliqnshop',
            'buybox_stores',
        ];

        $databaseName = Config::get('database.connections');

        foreach ($databaseName as $key => $table) {
            $connections[] = $key;

            $final_connection = array_filter($connections, function ($item) use ($ignoreArray) {
                return (!in_array($item, $ignoreArray));
            });
        }

        foreach ($final_connection as $key => $value) {

            $db_tables[$value] = Schema::connection($value)->getAllTables();
        }
        $web_table = (array) $db_tables['web'];
        $inventory_table = (array)$db_tables['inventory'];
        $order_table = (array) $db_tables['order'];
        $seller_table = (array) $db_tables['seller'];
        $shipntracking_table = (array) $db_tables['shipntracking'];
        $business_table = (array)$db_tables['business'];
        $oms_table = (array)$db_tables['oms'];
        $catalog_table = (array)$db_tables['catalog'];


        if (app()->environment() === 'local') {
            Log::Alert('local');
            foreach ($web_table as $key => $data) {
                $dat_web['web'][] = $data->Tables_in_mosh_360web;
            }
            foreach ($inventory_table as $key => $inv_data) {
                $data_inv['inventory'][] = $inv_data->Tables_in_mosh_inventory;
            }
            foreach ($order_table as $key => $ord_data) {
                $data_ord['order'][] = $ord_data->Tables_in_mosh_orders;
            }
            foreach ($seller_table as $key => $sell_data) {
                $data_seller['seller'][] = $sell_data->Tables_in_mosh_seller;
            }
            foreach ($shipntracking_table as $key => $ship_data) {
                $data_ship['shipntrack'][] = $ship_data->Tables_in_mosh_shipntrack;
            }
            foreach ($business_table as $key => $buis_data) {
                $data_busi['business'][] = $buis_data->Tables_in_mosh_business;
            }
            foreach ($oms_table as $key => $oms_data) {
                $data_oms['oms'][] = $oms_data->Tables_in_mosh_oms;
            }
            foreach ($catalog_table as $key => $cat_data) {
                $data_cat['catalog'][] = $cat_data->Tables_in_mosh_catalog;
            }
        }

        if (app()->environment() === 'staging') {
            Log::Alert('staging');
            foreach ($web_table as $key => $data) {
                $dat_web['web'][] = $data->Tables_in_stage_360web;
            }
            foreach ($inventory_table as $key => $inv_data) {
                $data_inv['inventory'][] = $inv_data->Tables_in_stage_inventory;
            }
            foreach ($order_table as $key => $ord_data) {
                $data_ord['order'][] = $ord_data->Tables_in_stage_orders;
            }
            foreach ($seller_table as $key => $sell_data) {
                $data_seller['seller'][] = $sell_data->Tables_in_stage_seller;
            }
            foreach ($shipntracking_table as $key => $ship_data) {
                $data_ship['shipntrack'][] = $ship_data->Tables_in_stage_shipntrack;
            }
            foreach ($business_table as $key => $buis_data) {
                $data_busi['business'][] = $buis_data->Tables_in_stage_business_catalog;
            }
            foreach ($oms_table as $key => $oms_data) {
                $data_oms['oms'][] = $oms_data->Tables_in_stage_oms;
            }
            foreach ($catalog_table as $key => $cat_data) {
                $data_cat['catalog'][] = $cat_data->Tables_in_stage_catalog;
            }
        }

        if (app()->environment() === 'production') {

            Log::Alert('Production');
            foreach ($web_table as $key => $data) {
                $dat_web['web'][] = $data->Tables_in_prod_360web;
            }
            foreach ($inventory_table as $key => $inv_data) {
                $data_inv['inventory'][] = $inv_data->Tables_in_prod_inventory;
            }
            foreach ($order_table as $key => $ord_data) {
                $data_ord['order'][] = $ord_data->Tables_in_prod_orders;
            }
            foreach ($seller_table as $key => $sell_data) {
                $data_seller['seller'][] = $sell_data->Tables_in_prod_seller;
            }
            foreach ($shipntracking_table as $key => $ship_data) {
                $data_ship['shipntrack'][] = $ship_data->Tables_in_prod_shipntrack;
            }
            foreach ($business_table as $key => $buis_data) {
                $data_busi['business'][] = $buis_data->Tables_in_prod_business_catalog;
            }
            foreach ($oms_table as $key => $oms_data) {
                $data_oms['oms'][] = $oms_data->Tables_in_prod_oms;
            }
            foreach ($catalog_table as $key => $cat_data) {
                $data_cat['catalog'][] = $cat_data->Tables_in_prod_catalog;
            }
        }


        $table_data = [
            $dat_web,
            $data_inv,
            $data_ord,
            $data_seller,
            $data_ship,
            $data_busi,
            $data_oms,
            $data_cat,
        ];
        $selected_data = [];
        $selected_datas =  Backup::select('connection', 'table_name', 'status')->where('status', 1)->get();
        foreach ($selected_datas as $key => $selected_db) {
            $selected_data[] = $selected_db->table_name;
        }
        return view('admin.dbbackup.index', compact('table_data', 'selected_data'));
    }

    public function backupsave(Request $request)
    {
        if ($request->ajax()) {


            Backup::query()->truncate();

            $request_data = json_encode($request->values);
            $request = explode(",", $request_data);
            $datas =  str_replace('"', '', $request);

            foreach ($datas as $key => $data) {
                $db_data = explode("|", $data);
                $connection = $db_data[0];
                $table = $db_data[1];

                $data_insert = [
                    'connection' => $connection,
                    'table_name' => $table,
                    'status' => 1,
                ];

                Backup::upsert($data_insert, ['table_name'], ['status']);
            }
            return response()->json('success');
        }
    }
}
