<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
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
    public function dashboard(Request $request)
    {

        $ware_lists = Inventory::with('warehouses')->get()->unique('warehouses');


        $request_ware_id = $request->ware_id;
        $url = "/inventory/stocks/list";
        if (isset($request_ware_id)) {
            $url = "/inventory/stocks/list/" . $request_ware_id;
        }


        if ($request->ajax()) {
            $data = Inventory::query()
                ->with(['warehouses' => function ($query) {
                    $query->select("id", "name");
                }])
                ->with(['shelves' => function ($query) {
                    $query->select("id", "name");
                }])
                ->when($request->ware_id, function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->ware_id);
                })
                ->orderBy('created_at', 'DESC')
                ->where('balance_quantity', '>', '0')
                ->get();


            return DataTables::of($data)
                ->addColumn('w_name', function ($row) {
                    if (isset($row->warehouses->name)) {
                        return $row->warehouses->name;
                    } else {
                        return 'NA';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    if (isset($row->created_at)) {
                        return $row->created_at->format('d-M-Y');
                    } else {
                        return 'NA';
                    }
                })
                ->rawColumns(['w_name'])
                ->make(true);
        }
        return view('inventory.stock.dashboard', compact('ware_lists', 'url', 'request_ware_id'));
    }



    public function eportinv(Request $request)
    {
        if ($request->ajax()) {
            $records = [];

            $records = Inventory::query()
                ->select('ship_id', 'asin', 'item_name', 'price', 'quantity', 'out_quantity', 'balance_quantity', 'bin', DB::raw('DATE_FORMAT(created_at,"%d %b %Y")'))

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
