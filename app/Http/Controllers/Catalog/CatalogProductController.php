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
        $sources = AsinSource::select('source')->groupBy('source')->get();
        $country_code = strtolower($request->country_code);
        $Tables = 'catalog' . $country_code . 's';

        if ($request->ajax()) {
            $data = '';
            $data = DB::connection('catalog')->select("SELECT * FROM $Tables ");
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('asin', function ($row) {
                 return '<a href="https://www.amazon.com/dp/' . $row->asin . '" target="_blank">' . $row->asin . '</a>';
                })
                ->editColumn('item_dimensions', function ($row) {
                    $dimension = 'NA';
                    $data = json_decode($row->item_dimensions);
                    if (isset($data->Height)) {
                        $dimension = '<p class="m-0 p-0">Height: ' . $data->Height->value . ' ' . $data->Height->Units . '</p>';
                    }
                    if (isset($data->Length)) {
                        $dimension .= '<p class="m-0 p-0">Length: ' . $data->Length->value . ' ' . $data->Length->Units . '</p>';
                    }
                    if (isset($data->Width)) {
                        $dimension .= '<p class="m-0 p-0">Width: ' . $data->Width->value . ' ' . $data->Width->Units . '</p>';
                    }

                    return $dimension;
                })
                ->editColumn('amount', function ($row) {
                    $amount = 'NA';
                    $amount = json_decode($row->list_price);
                    if (isset($amount)) {
                        $amount = "<p>" . $amount->CurrencyCode . "&nbsp;" . $amount->Amount . "</p>";
                    }
                    return $amount;
                })
                ->addColumn('weight', function ($row) {
                    $data = json_decode($row->item_dimensions);
                    if (isset($data->Weight)) {
                        $dimension = '<p class="m-0 p-0">Weight: ' . $data->Weight->value . ' ' . $data->Weight->Units . '</p>';
                    } else {
                        $dimension = 'NA';
                    }
                    return $dimension;
                })
                ->rawColumns(['amount', 'item_dimensions', 'weight', 'asin'])
                ->make(true);
        }

        return view('Catalog.product.index', compact('sources'));
    }

    public function Amazon(Request $request)
    {
        // $country_code = $request->country_code;
        // // $model_name = table_model_create(country_code:$country_code, model:'Asin_source', table_name:'asin_source_');
        // // $asins = $model_name->where('status', 0)->get(['asin', 'user_id']);
        // $asins = AsinSource::where('status', 0)->get(['asin', 'source', 'user_id']);

        // $redbean = new NewCatalog();
        // $redbean->RedBeanConnection();
        // $NewCatalogs = R::dispense('catalognews');
        // $NewCatalogs->asin = '';
        // R::store($NewCatalogs);

        // $count = 0;
        // $asin_source = [];
        // $class = 'catalog\\AmazonCatalogImport';
        // foreach ($asins as $asin) {

        //     if ($count == 10) {
        //         jobDispatchFunc($class, $asin_source, 'catalog');
        //         $asin_source = [];
        //         $count = 0;
        //     }
        //     $asin_source[] = [
        //         'asin' => $asin->asin,
        //         // 'source' => $country_code,
        //         'source' => $asin->source,
        //         'seller_id' => $asin->user_id
        //     ];
        //     $count++;
        // }
        // jobDispatchFunc($class, $asin_source, 'catalog');
        commandExecFunc("mosh:catalog-amazon-import");
        return redirect()->intended('/catalog/product');
    }

    public function ExportCatalog(Request $request)
    {
        $priority = $request->priority;
        $country_code = $request->source;
        commandExecFunc("mosh:catalog-export-csv ${priority} ${country_code} ");
        
        return redirect()->intended('/catalog/product');
    }

    public function GetCatalogFile(Request $request)
    {
        $folder = $request->catalog;
        $path = (Storage::path("excel/downloads/".$folder));
        $files = scandir($path);
        foreach($files as $key => $file)
        {
            if($key > 1)
            {
                $file_path = Storage::path("excel/downloads/".$folder."/".$file."/zip");
                $file_paths = scandir($file_path);
                foreach($file_paths as $key2 => $filename)
                {
                    if($key2 >1)
                    {
                        $catfile = basename($filename, '.zip');
                        $catalogfiles [][$file] = date("F d Y H:i:s.", filemtime($file_path . '/' . $filename));
                    }
                }
            }
        }
        return response()->json($catalogfiles);
    }

    public function DownloadCatalogIntocsv(Request $request, $country_code)
    {
        $folder = "catalog";
        $this->deletefile($folder, $country_code);
        $path = "excel/downloads/catalog/".$country_code."/zip/Catalog".$country_code.".zip";
        return Storage::download($path);
    }

    public function PriceExport(Request $request)
    {
        $country_code =  $request->country_code;
        commandExecFunc("mosh:catalog-price-export-csv --country_code=${country_code}");

        return redirect('/catalog/product')->with("success", "Catalog Price is Importing");
    }

    public function DownloadCatalogPrice($country_code)
    {
        $folder = "catalog_price";
        $this->deletefile($folder, $country_code);
        $path = "excel/downloads/catalog_price/".$country_code."/zip/".$country_code."_CatalogPrice.zip";
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
