<?php

namespace App\Http\Controllers\Catalog;

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\FileManagement;
use App\Models\Inventory\Dispose;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function insertCliqnshop()
    {

        $csv_data =  CSV_Reader('Cliqnshop/asin_import/cliqnshop_asin.csv');

        foreach ($csv_data as $data) {
            $asin[] = ($data['ASIN']);
            $category[] = ($data['Category']);
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

            $long_description = [
                $data['asin'] => []
            ];
            $short_description = [
                $data['asin'] => []
            ];



            if (isset($data['attributes'])) {

                $desc = json_decode($data['attributes'], true);
                if (isset($desc['bullet_point']) && !empty($desc['bullet_point'])) {

                    $bullet = $desc['bullet_point'];
                    foreach ($bullet as $key => $val) {
                        $short_description[$data['asin']] = $val['value'];
                        $long_description[$data['asin']][] = "<p>" . $val['value'] . "</p>";
                    }
                }
            }

            $asin =  $data['asin'];
            $item_name = $data['item_name'];
            $item_url = str_replace(' ', '-', $data['item_name']);
            $url = (strtolower($item_url));
            $Price_US_IN = $data['usa_to_in_b2c'];
            $usa_price = $data['us_price'];
            $Price_US_UAE2 = $data['usa_to_uae'];

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
                $color_key = (strtolower($color_label));
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

            $this->insertdata_cliqnshop(
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
                $short_description,
                $long_description
            );
        }
        po($image);
    }
    public function insertdata_cliqnshop($asin,  $item_name,  $brand,  $brand_label,  $color_key,  $label,  $length_unit,  $length_value,  $width_unit,  $width_value,  $Price_US_IN,  $image,  $short_description,  $long_description)
    {

        $date_time = Carbon::now();

        po('Insert started');
        $product_data = [
            'siteid' => '1.',
            // 'dataset' => '',
            'type' => 'default',
            'code' => $asin, //ASIN
            'label' => $item_name,
            'url' => $asin,
            'config' => '[]',
            // 'start' => NULL,
            // 'end' => NULL,
            'scale' => 1,
            // 'rating' => 0.00,
            // 'ratings' => 0,
            'instock' => 1,
            // 'target' => '',
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];
        DB::connection('cliqnshop')->table('mshop_product')->upsert($product_data, ['siteid', 'code']);

        $get_product = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $product_data['siteid'])->where('code', $product_data['code'])
            ->pluck('id')->ToArray();
        $get_product_id = $get_product[0];

        po('Inserted mshop_product');
        $category = [];

        $brand = [
            'siteid' => '1.',
            'code' =>  $brand,
            'label' =>  $brand_label,
            // 'status' => 1,
            // 'pos' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_supplier')->upsert($brand, ['siteid', 'code']);
        $get_brand = DB::connection('cliqnshop')->table('mshop_supplier')->where('siteid', $brand['siteid'])->where('code', $brand['code'])
            ->pluck('id')->ToArray();
        po('Inserted mshop_supplier');
        $get_brand_id = $get_brand[0];

        $attribute = [
            'siteid' => '1.',
            'key' => "product|color|" . $color_key,
            'type' => 'color',
            'domain' => 'product',
            'code' => $color_key,
            'label' => $label,
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_attribute')->upsert($attribute, ['siteid', 'code']);
        $get_attribute = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $attribute['siteid'])->where('code', $attribute['code'])
            ->pluck('id')->ToArray();
        $get_attribute_id = $get_attribute[0];


        $length_attribute = [
            'siteid' => '1.',
            'key' => "product|length|" . $length_value,
            'type' => 'length',
            'domain' => 'product',
            'code' => $length_value,
            'label' => $length_value . '  ' . $length_unit,
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_attribute')->upsert($length_attribute, ['siteid', 'code']);

        $get_attribute_length = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $length_attribute['siteid'])->where('code', $length_attribute['code'])
            ->pluck('id')->ToArray();
        $get_attribute_id_length = $get_attribute_length[0];

        $width_attribute = [
            'siteid' => '1.',
            'key' => "product|width|" . $width_value,
            'type' => 'width',
            'domain' => 'product',
            'code' => $width_value,
            'label' => $width_value . '  ' . $width_unit,
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_attribute')->upsert($width_attribute, ['siteid', 'code']);
        $get_attribute_width = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $width_attribute['siteid'])->where('code', $width_attribute['code'])
            ->pluck('id')->ToArray();
        $get_attribute_id_width = $get_attribute_width[0];



        po('Inserted mshop_attribute');

        $price = [
            'siteid' => '1.',
            'type' => 'default',
            'domain' => 'product',
            'label' => 'INR' . $Price_US_IN,
            'currencyid' => 'INR',
            // 'quantity' => 1,
            'value' => 500.00,
            // 'costs' => 0.00,
            // 'rebate' => 0.00,
            'taxrate' => '{"tax":"19.00"}',
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        $id_price = DB::connection('cliqnshop')->table('mshop_price')->insertGetId($price);
        po('Inserted mshop_price');

        $id_media = 0;

        if(isset($image[$asin])) {

            foreach ($image[$asin] as $val) {
                if ($val) {
                    $insert = $val;

                    $media = [
                        'siteid' => '1.',
                        'type' => 'default',
                        'fsname' => 'product',
                        // 'langid' => NULL,
                        'domain' => 'product',
                        'label' => '',
                        'link' => $insert,
                        'preview' => "{\"1\": \"${insert}\"}",
                        'mimetype' => 'image/jpeg',
                        // 'status' => 1,
                        'mtime' => $date_time,
                        'ctime' => $date_time,
                        'editor' => 'test',
                    ];

                    $id_media = DB::connection('cliqnshop')->table('mshop_media')->insertGetId($media);

                    $media_product_list = [
                        'siteid' => '1.',
                        'parentid' => $get_product_id,
                        'key' => 'media|default|' . $id_media,
                        'type' => 'default',
                        'domain' => 'media',
                        'refid' => $id_media,
                        // 'start' => NULL,
                        // 'end' => NULL,
                        'config' => '[]',
                        // 'pos' => 0,
                        // 'status' => 1,
                        'mtime' => $date_time,
                        'ctime' => $date_time,
                        'editor' => 'test',
                    ];
               
                    DB::connection('cliqnshop')->table('mshop_product_list')->upsert($media_product_list, ['siteid', 'parentid']);
                    // $image_get_id = DB::connection('cliqnshop')->table('mshop_media')->select('id')->where('link', $insert)->get();

                }
            }

        }   
        po('Inserted images');


        $text_short = [
            'siteid' => '1.',
            'type' => 'short',
            // 'langid' => NULL,
            'domain' => 'product',
            'label' => 'short description',
            'content' => json_encode($short_description),
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        $text_long = [
            'siteid' => '1.',
            'type' => 'long',
            // 'langid' => NULL,
            'domain' => 'product',
            'label' => 'long description',
            'content' => htmlspecialchars(json_encode($long_description)),
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        $id_text_short = DB::connection('cliqnshop')->table('mshop_text')->insertGetId($text_short);
        $id_text_long = DB::connection('cliqnshop')->table('mshop_text')->insertGetId($text_long);

        po('Inserted mshop_text');
        $domain_catalog = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'catalog|default|16',  //query catalog_code with mshop_catalog anf get ID fill here(In place of 16)
            'type' => 'default',
            'domain' => 'catalog',
            'refid' => 16,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];
        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_catalog, ['siteid', 'refid']);
        po('Inserted mshop_product_list 1');
        $domain_supplier = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'supplier|default|' . $get_brand_id,
            'type' => 'default',
            'domain' => 'supplier',
            'refid' =>  $get_brand_id,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_supplier, ['siteid', 'refid']);
        po('Inserted mshop_product_list 2');
        $domain_attribute = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'attribute|default|' . $get_attribute_id,
            'type' => 'default',
            'domain' => 'attribute',
            'refid' => $get_attribute_id,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute, ['siteid', 'refid']);
        $domain_attribute_length = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'attribute|default|' . $get_attribute_id_length,
            'type' => 'default',
            'domain' => 'attribute',
            'refid' => $get_attribute_id_length,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute_length, ['siteid', 'refid']);
        $domain_attribute_width = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'attribute|default|' . $get_attribute_id_width,
            'type' => 'default',
            'domain' => 'attribute',
            'refid' => $get_attribute_id_width,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute_width, ['siteid', 'refid']);
       
        po('Inserted mshop_product_list 3');
        $domain_price = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'price|default|' . $id_price,
            'type' => 'default',
            'domain' => 'price',
            'refid' => $id_price,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_price, ['siteid', 'refid']);
        po('Inserted mshop_product_list 4');
        $domain_media = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'media|default|' . $id_media,
            'type' => 'default',
            'domain' => 'media',
            'refid' => $id_media,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_media, ['siteid', 'refid']);
        po('Inserted mshop_product_list 5');
        $domain_text_short = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'text|default|' . $id_text_short,
            'type' => 'default',
            'domain' => 'text',
            'refid' => $id_text_short,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_text_short, ['siteid', 'refid']);
        po('Inserted mshop_product_list 6');
        $domain_text_long = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'text|default|' . $id_text_long,
            'type' => 'default',
            'domain' => 'text',
            'refid' => $id_text_long,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_text_long, ['siteid', 'refid']);
        po('Inserted mshop_product_list last(text)');

        $stock = [
            'siteid' => '1.',
            'prodid' => $get_product_id,
            'type' => 'default',
            'stocklevel' => 500,
            // 'backdate' => NULL,
            // 'timeframe' => '',
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];
        DB::connection('cliqnshop')->table('mshop_stock')->upsert($stock, ['siteid', 'prodid']);

        //index tables
        po('Inserted mshop_stock');
        $index_attribute = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'artid' => $get_product_id,
            'attrid' => $get_attribute_id,
            'listtype' => $domain_attribute['type'], // type from mshop_product_list
            'type' => $attribute['type'],
            'code' => $attribute['code'],
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert($index_attribute, ['siteid', 'prodid']);

        $index_attribute_length = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'artid' => $get_product_id,
            'attrid' => $get_attribute_id,
            'listtype' => $domain_attribute_length['type'], // type from mshop_product_list
            'type' => $length_attribute['type'],
            'code' => $length_attribute['code'],
            'mtime' => $date_time,
        ];
        DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert($index_attribute_length, ['siteid', 'prodid']);
        $index_attribute_width = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'artid' => $get_product_id,
            'attrid' => $get_attribute_id,
            'listtype' => $domain_attribute_width['type'], // type from mshop_product_list
            'type' => $width_attribute['type'],
            'code' => $width_attribute['code'],
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert($index_attribute_width, ['siteid', 'prodid']);

        $index_catalog = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'catid' => $domain_catalog['refid'],
            'listtype' => $domain_catalog['type'], // type from mshop_product_list
            'pos' => $domain_catalog['pos'], //from mshop_product_list
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_catalog')->upsert($index_catalog, ['siteid', 'prodid']);
        po('Inserted mshop_index catalog');
        $index_price = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'currencyid' => $price['currencyid'],
            'value' => $price['value'],
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_price')->upsert($index_price, ['siteid', 'prodid']);
        po('Inserted mshop_index_price');
        $index_supplier = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'supid' => $domain_supplier['refid'],
            'listtype' => $domain_supplier['type'],
            'latitude' => null,
            'longitude' => null,
            'pos' => $domain_supplier['pos'],
            'mtime' => $date_time,
        ];
        
        DB::connection('cliqnshop')->table('mshop_index_supplier')->upsert($index_supplier, ['siteid', 'prodid']);

        $index_supplier = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'supid' => $domain_supplier['refid'],
            'listtype' => $domain_supplier['type'],
            'latitude' => null,
            'longitude' => null,
            'pos' => $domain_supplier['pos'],
            'mtime' => $date_time,
        ];
     
        DB::connection('cliqnshop')->table('mshop_index_supplier')->upsert($index_supplier, ['siteid', 'prodid']);
        po('Inserted mshop_index suplier');


        $index_text = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'langid' => 'en',
            'url' => $product_data['url'],
            'name' => $product_data['label'],
            'content' => $product_data['code'] . '<pre>' . $product_data['label'] .
                '<pre>' . 'New arrivals' . '<pre>' . $brand['label'] . '<pre>' . $attribute['label'] . '<pre>'
                . $text_short['content'] . '<pre>' . $text_long['content'],
            'mtime' => $date_time,
        ]; //category hardcoded

        DB::connection('cliqnshop')->table('mshop_index_text')->upsert($index_text, ['siteid', 'prodid']);
        po('Inserted mshop_text');
        // $product_list = [
        //     'siteid' => '1.',
        //     'parentid' => $table_1,
        //     'key' =>
        // ]
        po('Inserted successfully');
        po($get_product_id);
    }
}
