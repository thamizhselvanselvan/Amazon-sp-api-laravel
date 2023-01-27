<?php

namespace App\Http\Controllers\Buybox_stores;

use Illuminate\Http\Request;
use App\Models\Aws_credential;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Buybox_stores\Product_Push;
use App\Models\order\OrderSellerCredentials;

class BuyBoxStoreController extends Controller
{
    public function index()
    {
        $stores = ['7', '8', '9', '10', '12', '20', '27'];

        $stores = Aws_credential::query()
            ->whereIN('seller_id', $stores)
            ->select('seller_id', 'store_name')
            ->get();
        return view('buybox_stores.index', compact('stores'));
    }

    public function latencyupdate(Request $request)
    {
        $asin = $request->asin;
        $store_id = $request->store;
        $latency = $request->latency;

        if ($latency == null || $asin == null || $store_id == null) {
            return redirect()->route('buybox.stores')->with('error', 'Please Add All the 3 Fields');
        }

        $request->validate([
            'asin' => 'required',
            'store' => 'required',
            'latency' => 'required',
        ]);
        Product::query()
            ->where(['asin' => $asin, 'store_id' => $store_id])
            ->update(['latency' => $latency]);

        return redirect()->route('buybox.stores')->with('success', 'Latency has Updated successfully');
    }

    public function exportall(Request $request)
    {
        commandExecFunc("mosh:export_all_stores_asins");
        return redirect()->route('buybox.stores')->with('success', 'Export Started Please Wait For Some time...');
    }

    public function exportdownload(Request $request)
    {
        $catalogfiles = [];
        $folder = $request->catalog;
        $path = (Storage::path($folder));
        $files = scandir($path);

        foreach ($files as $key => $file) {
            if ($key > 1) {
                $catalogfiles[$file] = date("F d Y H:i:s.", filemtime($path . '/' . $file));
            }
        }
        return response()->json($catalogfiles);
    }

    public function DownloadCataloglocal($index)
    {
        return Storage::download('aws-products/exports/' . $index);
    }

    public function get_price_push(Request $request)
    {

        if ($request->ajax()) {

            $results = Product_Push::query()
                ->select('id', 'store_id', 'product_sku', 'latency', 'push_price', 'base_price')
                ->orderBy('id', 'DESC')
                ->get();

            return DataTables::of($results)
                // ->addColumn('status', function ($file_management) {
                //     $process = $file_management['command_end_time'] == '0000-00-00 00:00:00' ? 'Processing...' : 'Processed';
                //     return $process;
                // })
                // ->rawColumns(['id', 'user_name', 'type', 'module', 'start_time', 'end_time', 'processed_time', 'status'])
                ->make(true);
        }

        return view('buybox_stores.sp_api_push');
    }

    public function storeslisting(Request $request)
    {

        $stores = OrderSellerCredentials::select('store_name', 'seller_id')
            ->where('buybox_stores', '1')
            ->distinct()
            ->get();

        $request_store_id = $request->store_id;
        $url = "/stores/listing/price";
        if (isset($request_store_id)) {
            $url = "/stores/listing/price/" . $request_store_id;
        }

        if ($request->ajax()) {
            Log::alert('fgh');
            Log::alert($request->store_id);
            $data = Product_Push::query()
                ->when($request->store_id, function ($query) use ($request) {
                    return $query->where('store_id', $request->store_id);
                })
                ->where('push_status', '0')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return view('buybox_stores.listing', compact('stores','url', 'request_store_id'));
    }
    public function storespriceupdated(Request $request)
    {
        $data =    Product_Push::query()
            ->where('push_status', '1')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = $row->asin . '_' . $row->product_sku . '_' . $row->store_id;
                    $actionBtn = "<a href='javascript:void(0)' value='$id'class='edit btn btn-success btn-sm'><i class='fas fa-refresh'></i> Update Price</a>";
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('buybox_stores.priceupdated');
    }
    public function updateprice($id){
        //command to execute
        // commandExecFunc('');

    }
}
