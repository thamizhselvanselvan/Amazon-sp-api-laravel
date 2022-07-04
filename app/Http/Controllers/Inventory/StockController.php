<?php

namespace App\Http\Controllers\Inventory;

use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\Inventory\Shipment;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Shipment_Inward_Details;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{

    public function stokes(Request $request)
    {

        if ($request->ajax()) {

            $data = Inventory::query()->with(['warehouses']);

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('warehouse_name', function ($data) {
                    return ($data->warehouses) ? $data->warehouses->name : "NA";
                })
                ->rawColumns(['warehouse_name'])
                ->make(true);
        }

        return view('inventory.stock.view');
    }

    public function dashboard()
    {
        
        $ware_lists = Shipment_Inward_Details::with('warehouses')->get()->unique('warehouses');
        // dd($ware_lists);exit;
        return view('inventory.stock.dashboard', compact('ware_lists'));
    }

    public function getlist(Request $request)
    {
        if ($request->ajax()) {
            $ware =  Inventory::with('warehouses')
        //   -> select('warehouses.*',$request->id)
            ->where('warehouse_id', $request->id)
            ->where('balance_quantity','>',0)
            ->get();

            return response()->json($ware);
        }
    }

    public function eportinv(Request $request)
    {
        $records = []; //Data from database
        $records = Inventory::query()
        ->select('warehouses.name',  'inventory.asin','inventory.ship_id', 'inventory.item_name', 'inventory.price', 'inventory.balance_quantity', 'inventory.created_at', 'inventory.bin')
        ->join('shipments', function($query) {
            $query->on("shipments.ship_id", "=", "inventory.ship_id");
        })
        ->join('warehouses', function($query) {
            $query->on("warehouses.id", "=", "shipments.warehouse");
        })->where('warehouses.id', $request->ware_id)->get();


        $headers = [
            'Warehouse Name',
            'Shipment ID',
            'ASIN',
            'Item Name',
            'Inwarding Price/Unit',
            'Quantity Left',
            'Inwarding Date',
            'Storage Bin'
        ];
        $exportFilePath = 'Inventory/WarehouseStocks.csv';// your file path, where u want to save
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);
        
        $csv_value = [];
        $count = 0;
        $writer->insertAll($records->toArray());
        return Storage::download($exportFilePath);
    }
}
