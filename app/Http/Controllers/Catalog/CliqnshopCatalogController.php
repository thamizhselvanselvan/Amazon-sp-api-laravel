<?php

namespace App\Http\Controllers\Catalog;

use League\Csv\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class CliqnshopCatalogController extends Controller
{
    public function catalogexport()
    {
        Artisan::call('Mosh:cliqnshop_catalog_export');
        return redirect('/catalog/product')->with("success", "CliqnshopCatalog Price is Exporting please Wait for Few Minuts.");
    }

    public function exportdownload(Request $request)
    {
        // $catalogfiles = [];
        // $folder = $request->catalog;
        // $path = Storage::path($folder);
        // $files = glob($path."\*.csv"); 
        // return response()->json('success');  

        $catalogfiles = [];
        $folder = $request->catalog;
        $path = Storage::path($folder);
        $files = scandir($path);

        return response()->json($files);
    }
    public function DownloadCatalogcloqnshop()
    {
        $path = "Cliqnshop/catalog.csv";
        return Storage::download($path);
    }
}
