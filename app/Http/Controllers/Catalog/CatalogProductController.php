<?php

namespace App\Http\Controllers\Catalog;

use file;
use config;
use RedBeanPHP\R;
use League\Csv\Reader;
use League\Csv\Writer;
use App\Models\Mws_region;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Models\FileManagement;
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
use App\Services\Catalog\AllPriceExportCsvServices;

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
            'priority' => 'required|in:1,2,3,4,All',
            'source' => 'required|in:IN,US,AE,SA',
        ]);

        $user_id = Auth::user()->id;
        $priority = $request->priority;
        $country_code = $request->source;

        $file_info = [
            "user_id" => $user_id,
            "type" => "CATALOG_EXPORT",
            "module" => "CATALOG_EXPORT_${country_code}_${priority}",
            "command_name" => "mosh:catalog-export-csv"
        ];
        FileManagement::create($file_info);
        fileManagement();
        // commandExecFunc("mosh:catalog-export-csv ${priority} ${country_code} ");

        return redirect('/catalog/product')->with("success", "Catalog is Exporting");
    }

    public function GetCatalogFile(Request $request)
    {

        $folder = $request->catalog;
        $catalogfiles = $this->getFileFromFolder($folder);
        return response()->json($catalogfiles);
    }

    public function getFileFromFolder($folder)
    {
        $catalogfiles = [];
        $path = (Storage::path("excel/downloads/" . $folder));
        $files = scandir($path);
        foreach ($files as $key => $file) {
            if ($key > 1) {
                $file_path = Storage::path('excel/downloads/' . $folder . '/' . $file);
                $file_paths = scandir($file_path);

                foreach ($file_paths as $key2 => $filename) {
                    if ($key2 > 1) {
                        $final_path = Storage::path('excel/downloads/' . $folder . '/' . $file . '/' . $filename);
                        $final_paths = scandir($final_path);
                        foreach ($final_paths as $key3 => $final_file) {
                            if ($key3 > 1) {

                                $search_paths = glob(Storage::path('excel/downloads/' . $folder . '/' . $file . '/' . $filename . '/zip/*'));
                                foreach ($search_paths as $search_path) {
                                    if (str_contains($search_path, '.zip')) {
                                        $catfile = basename($final_file, '.zip');
                                        $catalogfiles[$file][$filename] = date("F d Y H:i:s.", filemtime($final_path . '/' . $final_file));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $catalogfiles;
    }

    public function DownloadCatalogIntocsv(Request $request, $country_code, $priority)
    {
        $folder = "catalog";
        $this->deletefile($folder, $country_code);
        $path = "excel/downloads/catalog/" . $country_code . "/" . $priority . "/zip/Catalog" . $country_code . ".zip";
        return Storage::download($path);
    }

    public function PriceExport(Request $request)
    {
        $request->validate([
            'priority' => 'required|in:1,2,3,4',
            'source' => 'required|in:IN,US,AE,SA',

        ]);
        $date = '';
        $priority = $request->priority;
        $country_code = $request->source;
        $date = $request->export_date;
        $user_id = Auth::user()->id;

        $file_info = [
            "user_id" => $user_id,
            "type" => "CATALOG_PRICE_EXPORT",
            "module" => "CATALOG_PRICE_EXPORT_${country_code}_${priority}",
            "command_name" => "mosh:catalog-price-export-csv"

        ];
        FileManagement::create($file_info);
        fileManagement();
        // commandExecFunc("mosh:catalog-price-export-csv ${priority} ${country_code} '${date}'");
        return redirect('/catalog/product')->with("success", "Catalog Price is Exporting");
    }

    public function DownloadCatalogPrice($country_code, $priority)
    {
        $folder = "catalog_price";
        $this->deletefile($folder, $country_code);
        $path = "excel/downloads/catalog_price/" . $country_code . '/' . $priority . '/zip/' . $country_code . "_CatalogPrice.zip";
        return Storage::download($path);
    }

    public function deletefile($folder, $country_code)
    {
        $files = glob(Storage::path('excel/downloads/' . $folder . '/' . $country_code . '/*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function CatalogSearch(Request $request)
    {
        $request->validate([
            'source' => 'required|in:IN,US,AE,SA',
            'catalog_asins' => 'required',
        ]);
        $asin_array = [];
        $country_code = strtolower($request->source);
        $asins = array_unique(preg_split('/[\r\n| |:|,|.]/', $request->catalog_asins, -1, PREG_SPLIT_NO_EMPTY));
        $pricing = ($country_code == 'in') ? 'price.in_price, price.ind_to_uae, price.ind_to_sg, price.ind_to_sa' : ' us_price, usa_to_uae, usa_to_sg ';

        foreach ($asins as $key => $asin) {
            $asin_array[] = "'$asin'";
        }

        $asin_string = implode(',', $asin_array);
        $catalogs[] = DB::connection('catalog')
            ->select("SELECT cat.asin, cat.source, cat.dimensions, cat.item_name, cat.brand, cat.manufacturer, ${pricing}
                FROM catalognew${country_code}s  as cat
                JOIN pricing_${country_code}s as price
                ON cat.asin = price.asin
                where cat.asin IN ($asin_string)
            ");

        $header = [];
        $final_data = [];
        if (count($catalogs) > 0) {
            foreach ($catalogs[0] as $key => $catalog_value) {
                foreach ($catalog_value as $key1 => $data) {

                    if ($key1 != 'dimensions') {
                        $header[$key1] = $data;
                    } else {
                        $dimensions_array = json_decode($data);
                        $header['height'] = isset($dimensions_array[0]->package->height->value) ? round((float)$dimensions_array[0]->package->height->value, 3) : '';
                        $header['width'] = isset($dimensions_array[0]->package->width->value) ? round((float)$dimensions_array[0]->package->width->value, 3) : '';
                        $header['length'] = isset($dimensions_array[0]->package->length->value) ? round((float)$dimensions_array[0]->package->length->value, 3) : '';
                        $header['unit'] =  isset($dimensions_array[0]->package->length->unit) ? $dimensions_array[0]->package->length->unit : '';
                        $header['weight'] = isset($dimensions_array[0]->package->weight->value) ? round((float)$dimensions_array[0]->package->weight->value, 3) : '';
                        $header['weight_unit'] = isset($dimensions_array[0]->package->weight->unit) ? $dimensions_array[0]->package->weight->unit : '';
                    }
                }
                $final_data[] = $header;
            }
        }
        return response()->json($final_data);
    }

    public function CatalogWithPrice()
    {
        return view('Catalog.product.catalogwithprice');
    }

    public function CatalogWithPriceExport(Request $request)
    {
        if ($request->form_type == 'text-area') {
            $request->validate([
                'source'            => 'required|in:IN,US,AE,SA',
                'priority'          => 'required|in:1,2,3,4,All',
                'text_area_asins'   =>  'required',
                'header'            =>  'required',
            ]);

            // return $request->all();

            $csv_asin = [];
            $source = $request->source;
            $priority = $request->priority;

            $Asins =  preg_split('/[\r\n| |:|,|.]/', $request->text_area_asins, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($Asins as $Asin) {
                $csv_asin[] = ['ASIN' => $Asin];
            }

            $path = "CatalogWithPrice/asin.csv";

            if (!Storage::exists($path)) {
                Storage::put($path, '');
            }

            $csv_import = Writer::createFromPath(Storage::path($path), "w");
            $csv_import->insertOne(['ASIN']);
            $csv_import->insertAll($csv_asin);

            $headers = ['data' => implode('-', $request->header)];
            $user_id = Auth::user()->id;

            $file_info = [
                "user_id" => $user_id,
                "type" => "CATALOG_PRICE_EXPORT",
                "module" => "CATALOG_PRICE_EXPORT_${source}_${priority}",
                "command_name" => "mosh:export-catalog-with-price",
                "header" => json_encode($headers)
            ];

            FileManagement::create($file_info);
            fileManagement();

            // commandExecFunc("mosh:export-catalog-with-price ${source} ${priority} ${headers}");
        } elseif ($request->form_type == 'file_upload') {
            $request->validate([
                'asin' => 'required|mimes:txt,csv',
                'source'    => 'required',
                'priority'  => 'required',
            ]);

            $source = $request->source;
            $priority = $request->priority;

            $headers = ["data" => implode('-', $request->header)];

            $path = "CatalogWithPrice/asin.csv";
            $file = file_get_contents($request->asin);
            Storage::put($path, $file);

            $user_id = Auth::user()->id;

            $file_info = [
                "user_id" => $user_id,
                "type" => "CATALOG_PRICE_EXPORT",
                "module" => "CATALOG_PRICE_EXPORT_${source}_${priority}",
                "command_name" => "mosh:export-catalog-with-price",
                "header" => json_encode($headers)
            ];

            FileManagement::create($file_info);
            fileManagement();
        }
        return redirect('/catalog/export-with-price')->with("success", "Catalog with price is exporting");
    }

    public function CatalogWithPriceAsinUpload(Request $request)
    {
        $validation = $request->validate([
            'csvFile' => 'required|mimes:txt,csv',
        ]);
        if (!$validation) {
            return back()->with('error', "Please upload file to import it to the database");
        }
    }

    public function CatalogWithPriceFileShow(Request $request)
    {
        $folder = $request->catalog_with_price;
        $files = $this->getFileFromFolder($folder);
        return response()->json($files);
    }

    public function CatalogWithPriceDownloadTemplate()
    {
        $downloadFile = public_path('template/Catalog-Asin-Template.csv');
        return response()->download($downloadFile);
    }

    public function CatalogWithPriceDownload($country_code, $priority)
    {
        $folder = "catalog_price";
        $this->deletefile($folder, $country_code);
        $path = "excel/downloads/catalog_with_price/" . $country_code . '/' . $priority . '/zip/' . $country_code . "_CatalogPrice.zip";
        return Storage::download($path);
    }

    public function fileManagementMonitor(Request $request)
    {
        $command_end_time = fileManagementMonitoringNew($request->module_type);

        return response()->json($command_end_time);
    }

    public function ExportAllPrice(Request $request)
    {
        $destination = $request['source'];

        if ($destination == '') {
            return redirect('/catalog/product')->with("error", "Please Select Source...");
        }

        $user_id = Auth::user()->id;

        $file_info = [
            'user_id' => $user_id,
            'type' => 'EXPORT_ALL_PRICE',
            'module' => "CATALOG_PRICE_EXPORT_${destination}_ALL",
            'command_name' => 'mosh:ExportAllCatalogPrice',
        ];

        FileManagement::create($file_info);
        fileManagement();

        return redirect('/catalog/product')->with("success", "Catalog Price is Exporting");
    }
}
