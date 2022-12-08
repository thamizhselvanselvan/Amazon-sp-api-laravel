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

    public function index()
    {
return view('Cliqnshop.catalog');
    }
    public function catalogexport()
    {

        commandExecFunc("Mosh:cliqnshop_catalog_export");
        

        return redirect('/catalog/index')->with("success", "Cliqnshop Catalog Price is Exporting please Wait for Few Minuts.");

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
    public function DownloadCatalogcloqnshop($index)
    {
        return Storage::download('Cliqnshop/' . $index);
    }
}
