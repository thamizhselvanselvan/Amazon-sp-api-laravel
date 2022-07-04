<?php

namespace App\Http\Controllers\Inventory\Inwarding;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Shelve;
use App\Models\Inventory\Source;
use App\Models\Inventory\Vendor;
use App\Models\Inventory\Catalog;
use App\Models\Inventory\Shipment;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Services\SP_API\CatalogAPI;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Shipment_Inward;
use App\Models\Inventory\Shipment_Inward_Details;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;

class InventoryShipmentController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Shipment_Inward_Details::select("ship_id", "source_id")->distinct()->with(['vendors']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('source_name', function ($data) {
                    return ($data->vendors) ? $data->vendors->name : " NA";
                })
                ->addColumn('action', function ($row) {
                    $actionBtn  = '<div class="d-flex"><a href="/inventory/shipments/' . $row->ship_id . '" class="edit btn btn-success btn-sm"><i class="fas fa-eye"></i> View</a>';
                    $actionBtn .= '<div class="d-flex"><a href="/inventory/shipments/' . $row->ship_id . '/place" class="store btn btn-primary btn-sm ml-2"><i class="fas fa-box"></i> Bin Placement </a>';
                    $actionBtn .= '<div class="d-flex"><a href="/inventory/shipments/' . $row->ship_id . '/lable" class="lable btn btn-info btn-sm ml-2"><i class="fas fa-print"></i> Print label </a>';
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
        $currency_lists = Currency::get();
        return view('inventory.inward.shipment.create', compact('source_lists', 'ware_lists', 'currency_lists'));
    }

    public function show($id)
    {

        $view = Shipment_Inward_Details::where('ship_id', $id)->with(['warehouses', 'vendors'])->get();

        $warehouse_name = '';
        $vendor_name = '';
        $currency_id = '';

        $generator = new BarcodeGeneratorHTML();
        foreach ($view as $key => $bar) {
            //  dd($bar->ship_id);
            $bar_code = $generator->getBarcode($bar->ship_id, $generator::TYPE_CODE_93);
            $warehouse_name = $bar->warehouses->name;
            $vendor_name = $bar->vendors->name;
            $currency_id = $bar->currency;
        }

        $currency = Currency::get();
        $currency_array = [];
        foreach ($currency as $key => $cur) {
            $currency_array[$cur->id] = $cur->name;
        }


        return view('inventory.inward.shipment.view', compact('view', 'currency_array', 'bar_code', 'id', 'warehouse_name', 'vendor_name', 'currency_id'));
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

        $request->validate([
            'warehouse' => 'required',
            'currency' => 'required',

        ]);

        foreach ($request->asin as $key => $asin) {

            $items[] = [
                "asin" => $asin,
                "item_name" => $request->name[$key],
                "quantity" => $request->quantity[$key],
                "price" => $request->price[$key],
            ];
        }

        Shipment_Inward::insert([
            "warehouse_id" => $request->warehouse,
            "source_id" => $request->source,
            "ship_id" => $ship_id,
            "currency" => $request->currency,
            "shipment_count" => count($items),
            "created_at" => now(),
            "updated_at" => now()
        ]);

        foreach ($request->asin as $key1 => $asin1) {

            Shipment_Inward_Details::create([
                "warehouse_id" => $request->warehouse,
                "source_id" => $request->source,
                "ship_id" => $ship_id,
                "currency" => $request->currency,
                "asin" => $asin1,
                "item_name" => $request->name[$key1],
                "price" => $request->price[$key1],
                "quantity" => $request->quantity[$key1],
                "balance_quantity" => $request->quantity[$key1],
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }

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

            $data = Shipment_Inward_Details::query()->with(['vendors', 'warehouses']);


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
    public function store($id)
    {
        $store = Shipment_Inward_Details::where('ship_id', $id)->with(['warehouses', 'vendors'])->get();
        foreach ($store as $value) {

            $warehouse_id = $value->warehouse_id;
        }
        $rack = Rack::where('warehouse_id', $warehouse_id)->get();
        return view('inventory.inward.shipment.store', compact('store', 'rack'));
    }

    public function getRack($id)
    {
        $rack = Rack::where('warehouse_id', $id)->get();
        return response()->json($rack);
    }

    public function getShelve($id)
    {
        $binShelve = Shelve::where('rack_id', $id)->get();
        return response()->json($binShelve);
    }

    public function getBin($id)
    {
        $bin = Bin::where('shelve_id', $id)->get();
        return response()->json($bin);
    }

    public function placeship(Request $request)
    {
        foreach ($request->asin as $key1 => $asin) {

            $ship_id = $request->ship_id[$key1];
            Shipment_Inward_Details::where('ship_id', $ship_id)->where('asin', $asin)
                ->update([
                    'bin' => $request->bin[$key1],
                ]);
        }
        return response()->json(['success' => 'Shipment has stored successfully']);
    }


    public function printlable(Request $request, $id)
    {
        $lable = Shipment_Inward_Details::where('ship_id', $id)->with(['warehouses', 'vendors'])->get();

        foreach ($lable as $viewlable) {
            $data = $viewlable;
            $bar_code = [];

            $generator = new BarcodeGeneratorHTML();
            $bar_code[]  = $generator->getBarcode($data['asin'], $generator::TYPE_CODE_93);
           
        }
        
        return view('inventory.inward.shipment.lable', compact('viewlable','lable','data' ,'bar_code'));
    }

    public function Exportlable(Request $request)
    {
        $id = $request->id;
        $url = $request->url;
        $file_path =  'product/label' . $id . '.pdf';
        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }

        $exportToPdf = storage::path($file_path);
        Browsershot::url($url)
            //  ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->showBackground()
            ->savePdf($exportToPdf);

        return response()->json(["success" => "Export to PDF Successfully"]);
    }

    public function DownloadPdf($ship_id)
    {
        return Storage::download('/product/label' . $ship_id . '.pdf');
    }
}
