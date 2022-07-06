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
    public function dashboard()
    {
        
        $ware_lists = Shipment_Inward_Details::with('warehouses')->get()->unique('warehouses');
        return view('inventory.stock.dashboard', compact('ware_lists'));
    }

    public function getlist(Request $request)
    {
        if ($request->ajax()) {
            $ware =  Inventory::with('warehouses')->with('warehouses')
            ->where('warehouse_id', $request->id)
            ->where('balance_quantity','>',0)
            ->get();

            return response()->json($ware);
        }
    }

    public function eportinv(Request $request)
    {
        if ($request->ajax()) {
        $records = [];
        $records = Inventory::query()
        ->select(  'ship_id','asin', 'item_name', 'price','quantity','out_quantity', 'balance_quantity', 'created_at', 'bin')
        ->where('warehouse_id', $request->id)
        ->where('balance_quantity','>',0)
        ->get();


        $headers = [
           
            'Shipment ID',
            'ASIN',
            'Item Name',
            'Inwarding Price/Unit',
            'Quantity',
            'Outwarded',
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

        $writer->insertAll($records->toArray());
        return Storage::download($exportFilePath);
       
    }
}
}
