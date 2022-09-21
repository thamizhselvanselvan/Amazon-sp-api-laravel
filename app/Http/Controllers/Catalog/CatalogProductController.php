<?php

namespace App\Http\Controllers\Catalog;

use file;
use config;
use RedBeanPHP\R;
use App\Models\Mws_region;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Models\Catalog\catalog;
use SellingPartnerApi\Endpoint;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Configuration;
use App\Services\SP_API\API\NewCatalog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\CatalogItemsV0Api;

class CatalogProductController extends Controller
{

    public function Index(Request $request)
    {
        return view('Catalog.product.index');
    }

    public function Amazon(Request $request)
    {
        commandExecFunc("mosh:catalog-amazon-import");
        return redirect()->intended('/catalog/product');
    }

    public function ExportCatalog(Request $request)
    {
        $request->validate([
            'priority' => 'required|in:1,2,3',
            'source' => 'required|in:IN,US',
        ]);

        $priority = $request->priority;
        $country_code = $request->source;
        commandExecFunc("mosh:catalog-export-csv ${priority} ${country_code} ");
        
        return redirect('/catalog/product')->with("success", "Catalog is Exporting");;
    }

    public function GetCatalogFile(Request $request)
    {
        $catalogfiles = [] ;
        $folder = $request->catalog;
        $path = (Storage::path("excel/downloads/".$folder));
        $files = scandir($path);
        foreach($files as $key => $file)
        {
            if($key > 1)
            {
                $file_path = Storage::path('excel/downloads/'.$folder.'/'.$file);
                $file_paths = scandir($file_path);
                
                foreach($file_paths as $key2 => $filename)
                {
                    if($key2 >1)
                    {
                        $final_path = Storage::path('excel/downloads/'.$folder.'/'.$file.'/'.$filename);
                        $final_paths = scandir($final_path);
                        foreach($final_paths as $key3 => $final_file)
                        {
                            if($key3 > 1){
                                
                                $search_paths = glob(Storage::path('excel/downloads/'.$folder.'/'.$file.'/'.$filename.'/zip/*'));
                                foreach($search_paths as $search_path){
                                    if(str_contains($search_path, '.zip')){
                                        $catfile = basename($final_file, '.zip');
                                        $catalogfiles [$file][$filename] = date("F d Y H:i:s.", filemtime($final_path . '/' . $final_file));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return response()->json($catalogfiles);
    }

    public function DownloadCatalogIntocsv(Request $request, $country_code, $priority)
    {
        $folder = "catalog";
        $this->deletefile($folder, $country_code);
        $path = "excel/downloads/catalog/".$country_code."/".$priority."/zip/Catalog".$country_code.".zip";
        return Storage::download($path);
    }

    public function PriceExport(Request $request)
    {
        $request->validate([
            'priority' => 'required|in:1,2,3',
            'source' => 'required|in:IN,US',
        ]);
        $priority = $request->priority;
        $country_code = $request->source;
        commandExecFunc("mosh:catalog-price-export-csv ${priority} ${country_code}");
        return redirect('/catalog/product')->with("success", "Catalog Price is Exporting");
    }

    public function DownloadCatalogPrice($country_code, $priority)
    {
        $folder = "catalog_price";
        $this->deletefile($folder, $country_code);
        $path = "excel/downloads/catalog_price/".$country_code.'/'.$priority.'/zip/'.$country_code."_CatalogPrice.zip";
        return Storage::download($path);
    }

    public function deletefile($folder, $country_code)
    {
        $files = glob(Storage::path('excel/downloads/'.$folder.'/'.$country_code.'/*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
