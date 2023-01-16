<?php

namespace App\Http\Controllers\Buybox_stores;

use Illuminate\Http\Request;
use App\Models\Aws_credential;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Storage;

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
}
