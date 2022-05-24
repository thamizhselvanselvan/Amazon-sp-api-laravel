<?php

namespace App\Http\Controllers\Inventory\Outwarding;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Inventory\Vendor;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Destination;
use App\Models\Inventory\Outshipment;
use Yajra\DataTables\Facades\DataTables;

class InventoryOutwardShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Outshipment::select("ship_id", "destination_id")->distinct()->with(['vendors']);


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('destination_name', function ($data) {
                    return ($data->vendors) ? $data->vendors->name : " NA";
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex"><a href="/shipment/outwarding/view" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i>  View Shipment</a>';
                    return $actionBtn;
                })
                ->rawColumns(['destinations_name', 'action'])
                ->make(true);
        }




        return view('inventory.outward.shipment.index');
    }

    public function create()
    {
        $destination_lists = Vendor::where('type', 'Destination')->get();
        //  dd($destination_lists);
        $ware_lists = Warehouse::get();
        return view('inventory.outward.shipment.create', compact('destination_lists', 'ware_lists'));
    }

    public function autofinish(Request $request)
    {

        $data = Inventory::select("asin")->distinct()
            ->where("asin", "LIKE", "%{$request->asin}%")
            ->limit(50)
            ->get();

        return response()->json($data);
    }
    public function selectview(Request $request)
    {

        if ($request->ajax()) {

            return Inventory::query()->where('asin', $request->asin)->first();
        }
    }
    public function storeoutshipment(Request $request)
    {

        $shipment_id = random_int(1000, 9999);

        $createout = [];

        foreach ($request->asin as $key => $asin) {

            $items[] = [
                "asin" => $asin,
                "item_name" => $request->name,
                "quantity" => $request->quantity,
                "price" => $request->price,
            ];
        }

        $createout[] = [
            "Ship_id" => $shipment_id,
            "warehouse" => $request->warehouse,
            "currency" => $request->currency,
            "destination_id" => $request->destination,
            "items" => json_encode($items),
            "created_at" => now(),
            "updated_at" => now()
        ];
        // return $create;
        // exit;

        Outshipment::insert($createout);

        return response()->json(['success' => 'Shipment has Created successfully']);
    }
    public function outwardingview(Request $request)
    {

        if ($request->ajax()) {

            $data = Outshipment::query()->with(['vendors', 'warehouses']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('destination_name', function ($data) {
                    return ($data->vendors) ? $data->vendors->name : "NA";
                })
                ->editColumn('warehouse_name', function ($data) {
                    return ($data->warehouses) ? $data->warehouses->name : "NA";
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row['created_at'])->format('M d Y');
                })
                ->rawColumns(['destination_name', 'created_at', 'warehouse_name'])

                ->make(true);
        }

        return view('inventory.outward.shipment.view');
    }
}
