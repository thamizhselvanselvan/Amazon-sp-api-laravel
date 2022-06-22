<?php

namespace App\Http\Controllers\Inventory\Outwarding;

use Carbon\Carbon;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Vendor;
use App\Models\Inventory\Shipment;
use function React\Promise\reduce;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;

use App\Models\Inventory\Destination;
use App\Models\Inventory\Outshipment;
use Picqer\Barcode\BarcodeGeneratorHTML;
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
                    $actionBtn = '<div class="d-flex"><a href="/inventory/outwardings/' . $row->ship_id . '" class="edit btn btn-success btn-sm"><i class="fas fa-eye"></i> View</a>';
                    // $actionBtn .= '<div class="d-flex"><a href="/inventory/outwardings/' . $row->ship_id . '/outship" class="store btn btn-primary btn-sm ml-2"><i class="fas fa-box"></i> Storage </a>';
                    return $actionBtn;
                })
                ->rawColumns(['destinations_name', 'action'])
                ->make(true);
        }




        return view('inventory.outward.shipment.index');
    }

    public function create(Request $request)
    {

        $destination_lists = Vendor::where('type', 'Destination')->get();
        $currency_lists = Currency::get();
        $ware_list = [];
        $ware_lists = Shipment::select(["warehouse", "ship_id"])->with(['warehouses'])->get()->unique('warehouse');
        foreach ($ware_lists as $key => $ware) {
            $ware_list[] =  [
                'warehouse' => $ware['warehouse']
            ];
        }

        return view('inventory.outward.shipment.create', compact('destination_lists', 'ware_lists', 'currency_lists'));
    }

    public function show(Request $reques, $id)
    {
        $outview = Outshipment::where('ship_id', $id)->with(['warehouses', 'vendors'])->first();
        $data = json_decode($outview['items'], true);
        foreach ($data as $key => $val) {
            $items[] = [
                'asin' => $val['asin']
            ];
        }

        $generator = new BarcodeGeneratorHTML();
        $bar_code = $generator->getBarcode($outview->ship_id, $generator::TYPE_CODE_93);
        $currency = Currency::get();
        $currency_array = [];
        foreach ($currency as $key => $cur) {
            $currency_array[$cur->id] = $cur->name;
        }

        $place = Inventory::whereIn('asin', $items)->get();
        $loc = [];
        foreach ($place as $plc) {

            $loc[] = Bin::where('bin_id', $plc['bin'])->first();
        }

        return view('inventory.outward.shipment.view', compact('outview', 'currency_array', 'bar_code', 'loc'));
    }

    public function outstore($id)
    {
        $reduce = Outshipment::where('ship_id', $id)->with(['warehouses', 'vendors'])->first();

        $warehouse_id = ($reduce->warehouse);
        $rack = Rack::where('warehouse_id', $warehouse_id)->get();

        return view('inventory.outward.shipment.store', compact('reduce', 'rack'));
    }

    public function autofinish(Request $request)
    {
        $data = Inventory::select("asin", "id", "created_at")->distinct()
            ->where("asin", "LIKE", "%{$request->asin}%")
        
            ->orderBy('created_at')
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


        foreach ($request->asin as $key => $asin) {

            $items[] = [
                "asin" => $asin,
                "item_name" => $request->name[$key],
                "quantity" => $request->quantity[$key],
                "price" => $request->price[$key],
            ];
        }


        Outshipment::insert([
            "Ship_id" => $shipment_id,
            "warehouse" => $request->warehouse,
            "currency" => $request->currency,
            "destination_id" => $request->destination,
            "items" => json_encode($items),
            "created_at" => now(),
            "updated_at" => now()
        ]);


        foreach ($request->id as $key1 => $id) {

            if ($inventory = Inventory::where('id', $id)->first()) {

                Inventory::where('id', $id)->update([

                    'item_name' => $request->name[$key1],
                    'quantity' => $inventory->quantity - $request->quantity[$key1],
                ]);
            }
        }

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
