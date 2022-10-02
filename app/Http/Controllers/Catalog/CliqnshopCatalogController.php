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

        commandExecFunc("Mosh:cliqnshop_catalog_export");
        return redirect('/catalog/product')->with("success", "Cliqnshop Catalog Price is Exporting please Wait for Few Minuts.");
    }

    public function exportdownload(Request $request)
    {
        $catalogfiles = [];
        $folder = $request->catalog;
        $path = Storage::path($folder);
        $files = glob($path."\*.csv"); 
        $time =   date("F d Y H:i:s.", filemtime($path ));
        // return response()->json('success');  
        
        return response()->json($time);

//         $catalogfiles = [];
//         $folder = $request->catalog;
//         $path = Storage::path($folder);
//         $files = scandir($path);
// dd($files);
    }
    public function DownloadCatalogcloqnshop()
    {
        $path = "Cliqnshop/catalog.csv";
        return Storage::download($path);
    }
}
