<?php

namespace App\Http\Controllers\Inventory\Inwarding;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Inventory\Source;
use App\Models\Inventory\Vendor;
use App\Models\Inventory\Catalog;
use App\Models\Inventory\Shipment;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Services\SP_API\CatalogAPI;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class InventoryShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        
        // $user = Inventory::select('id')->get();

        //  dd($user);
        // exit;

        if ($request->ajax()) {

            $data = Shipment::select("ship_id", "source_id")->distinct()->with(['vendors']);


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('source_name', function ($data) {
                    return ($data->vendors) ? $data->vendors->name : " NA";
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex"><a href="/inventory/shipments/' . $row->ship_id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i>  View Shipment</a>';
                    return $actionBtn;
                })
                ->rawColumns(['source_name', 'action'])
                ->make(true);
        }


        return view('inventory.inward.shipment.index');
    }
    public function create()
    {

        $source_lists = Vendor::where('type', 'Source')->get();
        $ware_lists = Warehouse::get();
        return view('inventory.inward.shipment.create', compact('source_lists', 'ware_lists'));
    }

    public function show($id)
    {

        $view = Shipment::where('ship_id', $id)->with(['warehouses', 'vendors'])->first();

        return view('inventory.inward.shipment.view', compact('view'));
    }


    public function createView(Request $request)
    {

        return view('inventory.inward.shipment.create');
    }

    public function autocomplete(Request $request)
    {
        $data = Product::select("asin1", "item_name")->distinct()
            ->where("asin1", "LIKE", "%{$request->asin}%")
            ->limit(50)
            ->get();

        if ($data->count() > 0) {
            return response()->json($data);
        }

        $data = Catalog::select("asin", "item_name")->distinct()
            ->where("asin", "LIKE", "%{$request->asin}%")
            ->limit(50)
            ->get();

        if ($data->count() > 0) {
            $datas[] = [
                'asin' => $data->asin1
            ];
            return response()->json($data);
        }

        $catalogApi = new CatalogAPI();
        $data[] = $catalogApi->getAsin($request->asin);

        return response()->json($data);
    }

    public function selectView(Request $request)
    {

        if ($request->ajax()) {

            return Product::query()->where('asin1', $request->asin)->first();
        }
    }


    public function storeshipment(Request $request)
    {

        $ship_id = random_int(1000, 9999);
        $items = [];

        foreach ($request->asin as $key => $asin) {

            $items[] = [
                "asin" => $asin,
                "item_name" => $request->name[$key],
                "quantity" => $request->quantity[$key],
                "price" => $request->price[$key],
            ];
        }

        Shipment::insert( [
            "Ship_id" => $ship_id,
            "warehouse" => $request->warehouse,
            "currency" => $request->currency,
            "source_id" => $request->source,
            "items" => json_encode($items),
            "created_at" => now(),
            "updated_at" => now()
        ]);

        foreach ($request->asin as $key => $asin) {

            $createin[] = [
                "warehouse_id" => $request->warehouse,
                "asin" => $asin,
                "item_name" => $request->name[$key],
                "quantity" => $request->quantity[$key],
                "created_at" => now(),
                "updated_at" => now()
            ];
        }

        Inventory::insert($createin);
        // foreach ($asin as $key => $asin) {
            // if (Inventory::where('asin', $asin)->exists()) {
            //     // $quant = Inventory::select('quantity')->get();
               
            // } else {
            //     Inventory::insert($createin);
            // }
    
        // }
        foreach ($request->asin as $key => $asin) {

            $createcat[] = [
                "asin" => $asin,
                "item_name" => $request->name[$key],
                "created_at" => now(),
                "updated_at" => now()
            ];
        }
        Catalog::insert($createcat);
        return response()->json(['success' => 'Shipment has Created successfully']);
    }

    public function inwardingdata(Request $request)
    {
        if ($request->ajax()) {

            $data = Shipment::query()->with(['vendors', 'warehouses']);


            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('source_name', function ($data) {
                    return ($data->vendors) ? $data->vendors->name : "NA";
                })
                ->editColumn('warehouse_name', function ($data) {
                    return ($data->warehouses) ? $data->warehouses->name : "NA";
                })



                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row['created_at'])->format('M d Y');
                })
                ->rawColumns(['source_name', 'created_at', 'warehouse_name'])
                ->make(true);
        }


        return view('inventory.inward.shipment.view');
    }
}
