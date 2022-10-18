<?php

namespace App\Http\Controllers\Inventory;

use League\Csv\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inventory\Shipment_Inward_Details;

class StockController extends Controller
{
    public function dashboard()
    {
    
        $ware_lists = Inventory::with('warehouses')->get()->unique('warehouses');
        return view('inventory.stock.dashboard', compact('ware_lists'));
    }

    public function getlist(Request $request)
    {
        if ($request->ajax()) {
            $ware =  Inventory::with('warehouses','shelves')
                ->where('warehouse_id', $request->id)
                ->where('balance_quantity', '>', 0)
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json($ware);
        }
    }

    public function eportinv(Request $request)
    {
        if ($request->ajax()) {
            $records = [];
            
            $records = Inventory::query()
                ->select('ship_id', 'asin', 'item_name', 'price', 'quantity', 'out_quantity', 'balance_quantity', 'bin' , DB::raw('DATE_FORMAT(created_at,"%d %b %Y")'))

                ->where('warehouse_id', $request->id)
                ->where('balance_quantity', '>', 0)
                ->get();

            $headers = [

                'Shipment ID',
                'ASIN',
                'Item Name',
                'Inwarding Price/Unit',
                'Quantity',
                'Outwarded',
                'Quantity Left',
                'Storage Shelve',
                'Inwarding Date'
            ];
            $exportFilePath = 'Inventory/WarehouseStocks.csv'; // your file path, where u want to save
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);

            $writer->insertAll($records->toArray());

        }
    }
    public function downexp($id)
    {
        return Storage::download('/Inventory/WarehouseStocks' .  '.csv');
    }
}
