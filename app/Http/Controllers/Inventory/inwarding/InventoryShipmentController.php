<?php

namespace App\Http\Controllers\Inventory\Inwarding;

use Carbon\Carbon;
use Nette\Utils\Json;
use App\Models\Product;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Tag;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Shelve;
use App\Models\Inventory\Vendor;
use App\Models\Inventory\Catalog;
use App\Models\Inventory\Country;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Services\SP_API\CatalogAPI;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use League\Glide\Manipulators\Encode;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inventory\Shipment_Inward;
use App\Models\Inventory\Shipment_Inward_Details;

class InventoryShipmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Shipment_Inward_Details::select("ship_id", "source_id", "created_at")->distinct()->with(['vendors'])->orderby('created_at','DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('source_name', function ($data) {
                    return ($data->vendors) ? $data->vendors->name : " NA";
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row['created_at'])->format('M d Y');
                })
                ->addColumn('action', function ($row) {
                    $actionBtn  = '<div class="d-flex"><a href="/inventory/shipments/' . $row->source_id.'/'. $row->ship_id . '" class="edit btn btn-success btn-sm"><i class="fas fa-eye"></i> View</a>';
                    $actionBtn .= '<div class="d-flex"><a href="/inventory/shipments/' . $row->source_id .'/'. $row->ship_id . '/place" class="store btn btn-primary btn-sm ml-2"><i class="fas fa-box"></i> Bin Placement </a>';
                    $actionBtn .= '<div class="d-flex"><a href="/inventory/shipments/' . $row->source_id . '/' . $row->ship_id . '/lable" class="lable btn btn-info btn-sm ml-2"><i class="fas fa-print"></i> Print label </a>';
                    return $actionBtn;
           
                })

                ->rawColumns(['source_name', 'action', 'date'])
                ->make(true);
        }

        return view('inventory.inward.shipment.index');
    }

    public function create()
    {

        $source_lists = Vendor::where('type', 'Source')->get();
        $ware_lists = Warehouse::get();
        $currency_lists = Currency::get();
        $tags = Tag::get();
        return view('inventory.inward.shipment.create', compact('source_lists', 'ware_lists', 'currency_lists','tags'));
    }

    public function show($source, $id)
    {

        $view = Shipment_Inward_Details::where('ship_id', $id)->where('source_id', $source)->with(['warehouses', 'vendors'])->get();

        $warehouse_name = '';
        $vendor_name = '';
        $currency_id = '';

        $generator = new BarcodeGeneratorHTML();
        $bar_code = '';
        foreach ($view as $key => $bar) {

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
        $tag = Tag::select("name")->get();

        $asins = preg_split('/[\r\n| |:|,]/', $request->asin, -1, PREG_SPLIT_NO_EMPTY);

        $data = [];
        $asinCol = [];
        $sourcecol = [];
        foreach ($asins as $asin) {

            $data[$asin] = [
                'asin' => $asin,
                'source' => $request->source
            ];

            $asinCol[] = $asin;
            $sourcecol[] =   $request->source;
        }

        $source_list = Vendor::query()
            ->select("country")
            ->whereIn("id", $sourcecol)
            ->first();


        $wantedsrc = Country::query()
            ->select("code")
            ->whereIn("id", $source_list)
            ->first();



        $catalog = Catalog::query()
            ->select("asin", "item_name")
            ->whereIn("asin", $asinCol)
            ->get()->unique('asin')->groupBy('asin');

        $catalog_insert = [];
        $filtere_data = [];
        foreach ($data as $asin_key => $val) {
            if (isset($catalog[$asin_key])) {

                $name = (string)$catalog[$asin_key]->first()->item_name;

                if (strlen($name) > 0) {

                    $filtere_data[$asin_key] = $catalog[$asin_key];
                } else {
                    $filtere_data[$asin_key] = "NA";
                }
            } else {
                $filtere_data[$asin_key] = "NA";
                $catalog_insert[$asin_key] = [
                    'asin' => $val['asin'] ?? $asin_key,
                    'source' => $wantedsrc->code
                ];
            }
        }

        if (count($catalog_insert) > 0) {
            Catalog::insert($catalog_insert);
            commandExecFunc("mosh:inventory_catalog_import");
           
        }


        return response()->json(['success' => 'Data  successfully added', 'data' =>   $filtere_data, $tag]);
    }

    public function refreshtable(Request $request)
    {

        $tag = Tag::select("name")->get();
        $asins = preg_split('/[\r\n| |:|,]/', $request->asin, -1, PREG_SPLIT_NO_EMPTY);

        $data = [];
        $asinCol = [];

        foreach ($asins as $asin) {

            $data[$asin] = [
                'asin' => $asin,
            ];
            $asinCol[] = $asin;
        }
        $catalog = Catalog::query()
            ->select("asin", "item_name")
            ->whereIn("asin", $asinCol)
            ->get()->unique('asin')->groupBy('asin');

        return response()->json(['success' => 'Data  successfully Refreshed', 'data' => $catalog, $tag]);
    }

    public function selectView(Request $request)
    {
        if ($request->ajax()) {

            return Product::query()->where('asin1', $request->asin)->first();
        }
    }

    public function storeshipment(Request $request)
    {
        $uniq= random_int(1000, 99999);
        $ship_id ='INW'. $uniq;
        $items = [];

        $request->validate([
            'warehouse' => 'required',
            'currency' => 'required',
        ]);
        
        foreach ($request->asin as $key => $asin) {

            $items[] = [
                "asin" => $asin,
                "item_name" => $request->name[$key],
                "tag" => $request->tag[$key],
                "quantity" => $request->quantity[$key],
                "price" => $request->price[$key],
            ];
        }

        foreach ($request->asin as $key1 => $asin1)
         {

                      Shipment_Inward::insert([
                          "warehouse_id" => $request->warehouse,
                          "source_id" =>  $request->source[$key1],
                          "ship_id" => $ship_id,
                          "currency" => $request->currency,
                          "shipment_count" => count($items),
                          "created_at" => now(),
                          "updated_at" => now()
                      ]);
          }
 
        foreach ($request->asin as $key1 => $asin1) {

            Shipment_Inward_Details::create([
                "warehouse_id" => $request->warehouse,
                "source_id" =>  $request->source[$key1],
                "ship_id" => $ship_id,
                "currency" => $request->currency,
                "asin" => $asin1,
                "item_name" => $request->name[$key1],
                "tag" => $request->tag[$key1],
                "price" => $request->price[$key1],
                "quantity" => $request->quantity[$key1],
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }

        foreach ($request->asin as $key1 => $asin1) {

            Inventory::create([
                "warehouse_id" => $request->warehouse,
                "source_id" =>  $request->source[$key1],
                "ship_id" => $ship_id,
                "asin" => $asin1,
                "price" => $request->price[$key1],
                "item_name" => $request->name[$key1],
                "tag" => $request->tag[$key1],
                "quantity" => $request->quantity[$key1],
                "balance_quantity" => $request->quantity[$key1],
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }

        return response()->json(['success' => 'Shipment has Created successfully']);
    }

    public function store($source,$id)
    {
        $warehouse_id ='';
        $store = Inventory::where('ship_id', $id)->where('source_id',$source)->with(['warehouses', 'vendors'])->get();
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
            Inventory::where('ship_id', $ship_id)->where('asin', $asin)
                ->update([
                    'bin' => $request->shelve[$key1],
                ]);
        }
        return response()->json(['success' => 'Shipment has stored successfully']);
    }

    public function printlable(Request $request,$source, $id)
    {
        $lable = Shipment_Inward_Details::where('ship_id', $id)->where('source_id', $source)->with(['warehouses', 'vendors'])->get();
        $quant = [];
        $total = 0;
        foreach ($lable as $key => $val) {
            $quant[] += $val->quantity;
        }
        foreach ($quant as $key => $jay) {
            $total += $jay;
        }

        $bar_code = [];
        foreach ($lable as $viewlable) {
            $data = $viewlable;

            //$generator = new BarcodeGeneratorHTML();
            //$bar_code[]  = $generator->getBarcode($data['asin'], $generator::TYPE_CODE_93);
            $generator = new BarcodeGeneratorPNG();
            $bar_code[]  = base64_encode($generator->getBarcode($data['asin'], $generator::TYPE_CODE_93));
        }

        return view('inventory.inward.shipment.lable', compact('viewlable', 'lable', 'data', 'bar_code', 'quant', 'total'));
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
           // ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->showBackground()
            ->savePdf($exportToPdf);

        return response()->json(["success" => "Export to PDF Successfully"]);
    }

    public function DownloadPdf($ship_id)
    {
        return Storage::download('/product/label' . $ship_id . '.pdf');
    }
}