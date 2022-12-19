<?php

namespace App\Http\Controllers\Catalog;

use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\FileManagement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

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
        return Storage::download('Cliqnshop/catalog/' . $index);
    }

    public function asinCsvDownload()
    {
        return response()->download(public_path("template/CliqnshopCatalog.csv"));
    }

    public function cliqnshopImport(Request $request)
    {
        if ($request->cliqnshop_csv == '') {
            return back()->with('error', "Please upload file to import it to the database or no file choosen");
        }


        $request->validate([
            'cliqnshop_csv' => 'required',
        ]);
        $user_id = Auth::user()->id;
        $file = file_get_contents($request->cliqnshop_csv);
        $import_file_time = date('Y-m-d-H-i-s');
        $path = "Cliqnshop/asin_import/cliqnshop_asin.csv";
        Storage::put($path, $file);

        $file = $request->cliqnshop_csv;
        // $file_name = $file->getClientOriginalName();
        // $file_info = [
        //     'user_id' => $user_id,
        //     'type' => 'Cliqnsho catalog export',
        //     'module' => "Cliqnshop",
        //     'file_name' => $file_name,
        //     'file_path' => $path,
        //     'command_name' => 'mosh:export_catalog_imported_asin',
        // ];
        // FileManagement::create($file_info);
        // fileManagement();
        if (!Storage::exists($path)) {
            return false;
        } else {

            commandExecFunc("mosh:export_catalog_imported_asin");
        }


        return back()->with('success', 'Cliqnshop Catalog file has been uploaded successfully !');
    }

    public function uploaded_export_download(Request $request)
    {
        if ($request->ajax()) {
           
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
    }

    public function Download_uploaded_asin_catalog($index)
    {
        return Storage::download('Cliqnshop/imported_cat/' . $index);
    }


}
