<?php

namespace App\Http\Controllers\Catalog;

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\FileManagement;
use App\Models\Inventory\Dispose;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Services\Cliqnshop\CliqnshopCataloginsert;
use App\Services\AWS_Business_API\Search_Product_API\Search_Product;

class CliqnshopCatalogController extends Controller
{

    public function index()
    {
        $countrys = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.catalog', compact('countrys'));
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
        if ($request->cliqnshop_csv == '' || $request->country == '') {
            return back()->with('error', "Please upload file to import it to the database or no Country choosen");
        }
        $request->validate([
            'cliqnshop_csv' => 'required',
            'country' => 'required',
        ]);
        $site_id = $request->country;
        $file = file_get_contents($request->cliqnshop_csv);
        $path = "Cliqnshop/asin_import/cliqnshop_asin.csv";

        Storage::put($path, $file);

        $file = $request->cliqnshop_csv;
        if (!Storage::exists($path)) {
            return false;
        } else {

            commandExecFunc("mosh:export_catalog_imported_asin");
            $this->insertCliqnshop($site_id);
            return back()->with('success', 'Cliqnshop Catalog file has been uploaded successfully !');
        }
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

    public function insertCliqnshop($site_id)
    {

        $csv_data =  CSV_Reader('Cliqnshop/asin_import/cliqnshop_asin.csv');

        foreach ($csv_data as $data) {
            $asin[] = ($data['ASIN']);
            $category[$data['ASIN']] = ($data['Category']);
        }

        $headers = [
            'catalognewuss.asin',
            'catalognewuss.brand',
            'catalognewuss.images',
            'catalognewuss.item_name',
            'catalognewuss.browse_classification',
            'catalognewuss.dimensions',
            'catalognewuss.attributes',
            'catalognewuss.color',
            'pricing_uss.usa_to_in_b2c',
            'pricing_uss.us_price',
            'pricing_uss.usa_to_uae',

        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');
        $result = $table_name->select($headers)
            ->join('pricing_uss', 'catalognewuss.asin', '=', 'pricing_uss.asin')
            ->whereIn('catalognewuss.asin', $asin)
            ->get()->toArray();

        foreach ($result as $data) {

            $img1 = [
                "Images1" => '',
                "Images2" => '',
                "Images3" => '',
                "Images4" => '',
                "Images5" => '',
                "Images6" => '',
                "Images7" => '',
                "Images8" => '',
                "Images9" => '',
                "Images10" => '',
            ];

            $imagedata = json_decode($data['images'], true);

            if (isset($imagedata[0]['images'])) {

                foreach ($imagedata[0]['images'] as $counter => $image_data_new) {
                    $counter++;
                    if (array_key_exists("link", $image_data_new)) {

                        if ($img1["Images${counter}"] = $image_data_new['height'] == 75) {

                            $img1["Images${counter}"] = '';
                        } else {
                            $img1["Images${counter}"] = $image_data_new['link'];
                        }
                    } else {
                        $img1["Images${counter}"] = null;
                    }
                    if ($counter == 10) {
                        break;
                    }
                }
            } else {
                for ($i = 1; $i <= 5; $i++) {
                    $img1["Images${i}"] = null;
                }
            }
            $image[$data['asin']] = $img1;

            $long_description = '';

            $short_description = [
                $data['asin'] => []
            ];

            if (isset($data['attributes'])) {

                $desc = json_decode($data['attributes'], true);
                if (isset($desc['bullet_point']) && !empty($desc['bullet_point'])) {

                    $bullet = $desc['bullet_point'];
                    foreach ($bullet as $key => $val) {

                        $short_description[$data['asin']] = $val['value'];
                        $long_description .=  '<p>' . $val['value'];
                    }
                }
            }
           
            $asin =  $data['asin'];
            $item_name = $data['item_name'];
            $item_url = str_replace(' ', '-', $data['item_name']);
            $url = (strtolower($item_url));
           
            $country = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $site_id)->select('code')->get();
            
            $Price_US_IN = $data['usa_to_in_b2c'];
            if (isset($country['0']->code)) {
                if (($country['0']->code) == 'in') {
                    $Price_US_IN = $data['usa_to_in_b2c'];
                } else if ($country['0']->code == 'uae') {
                    $Price_US_IN  = $data['usa_to_uae'];
                }
            }


            $catalog_code = json_decode($data['browse_classification'], true);
            $cat_code = 'new';
            $cat_code_type = 'all';
            if ($catalog_code == null) {
                $cat_code = 'new';
                $cat_code_type = 'all';
            } else if (isset($catalog_code['classificationId'])) {
                $cat_code = $catalog_code['classificationId'];
                $cat_code_type = null;
            }
            //brand
            $brand_label = 'NA';
            if ($data['brand']) {

                $brand_label = $data['brand'];
            }
            $brand_place = str_replace(' ', '', $data['brand']);
            $brand =  substr(strtolower($brand_place), 0, 10);

            $color_code = 'NA';
            $color_label = 'NA';
            $label = 'NA';
            $color_key = 'NA';
            if (isset($data['color'])) {
                $color_code = str_replace(' ', '', $data['color']);
                $color_label = $data['color'];
                $label =  ucfirst($color_label);
                $color_key = substr(strtolower($color_label), 0, 10);
            }

            //dimensions Fetch
            $length_unit = '';
            $length_value = '';
            $width_unit = '';
            $width_value  = '';
            if (isset($data['dimensions'])) {

                $length_unit  = '';
                $length_value = '';
                $dim = json_decode($data['dimensions'], true);
                if (isset($dim[0]['item']['length'])) {
                    $length_unit  = $dim[0]['item']['length']['unit'];
                    $length_value  = $dim[0]['item']['length']['value'];
                }

                $width_unit  = '';
                $width_value = '';
                if (isset($dim[0]['item']['width'])) {
                    $width_unit  = $dim[0]['item']['width']['unit'];
                    $width_value  = $dim[0]['item']['width']['value'];
                }
            }

            if ($category[$asin] == '') {
                $category_code = 'demo-new';
            } else {

                $category_code = $category[$asin];
            }
            $keyword = '';
            $insert_service = new CliqnshopCataloginsert();
            $insert_service->insertdata_cliqnshop(
                $site_id,
                $category_code,
                $asin,
                $item_name,
                $brand,
                $brand_label,
                $color_key,
                $label,
                $length_unit,
                $length_value,
                $width_unit,
                $width_value,
                $Price_US_IN,
                $image,
                $keyword,
                $short_description,
                $long_description
            );
        }

        return back()->with('success', 'uploading please wait... !');
    }

    public function CliqnshopProductSearchRequest(Request $request)
    {
        $search_data = $request->all();

        $searchKey = $search_data['search'];
        $searchKey = str_replace(' ', '_', $searchKey);
        $siteId = $search_data['siteId'];
        $source = $search_data['source'];
        //Process Management start
        $process_manage = [
            'module'             => 'Cliqnshop Product Search',
            'description'        => 'Search product from cliqnshop',
            'command_name'       => 'mosh:cliqnshop-product-search',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $ApiCall = new Search_Product();
        $result = $ApiCall->SearchProductByKey($searchKey, $siteId, $source);

        date_default_timezone_set('Asia/Kolkata');
        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
        return response()->json('successfully');
        // commandExecFunc("mosh:cliqnshop-product-search ${searchKey} ${siteId} ${source}");
    }
}
