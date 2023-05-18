<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\TestZoho;
use App\Models\Mws_region;
use App\Events\EventManager;
use App\Models\Admin\Backup;
use App\Models\Aws_credential;
use App\Services\Zoho\ZohoApi;
use PhpParser\Node\Stmt\Foreach_;
use App\Models\Inventory\Shipment;
use App\Models\ShipNTrack\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Services\SP_API\API\NewCatalog;
use Illuminate\Support\Facades\Storage;
use App\Services\Catalog\PriceConversion;
use App\Models\ShipNTrack\Courier\Courier;
use App\Models\Inventory\Shipment_Inward_Details;
use App\Services\Cliqnshop\CliqnshopCataloginsert;
use App\Http\Controllers\Inventory\StockController;
use App\Models\ShipNTrack\Courier\StatusManagement;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;
use JeroenNoten\LaravelAdminLte\View\Components\Widget\Card;
use App\Services\AWS_Business_API\AWS_POC\Search_Product_Request;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

Route::get('sanju/test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');
Route::get('sanju/test/controller', 'SanjayTestController@index');

Route::get('sanju/test/images', function () {
    $data = ('[{"marketplaceId":"ATVPDKIKX0DER","images":[{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/71OWSOnk+zL.jpg","height":1000,"width":1000},{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/61Wpkl8oVDL.jpg","height":500,"width":500},{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/61Wpkl8oVDL._SL75_.jpg","height":75,"width":75},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/71--NVjLvhL.jpg","height":1000,"width":1000},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/61XF+tw0r0L.jpg","height":500,"width":500},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/61XF+tw0r0L._SL75_.jpg","height":75,"width":75},{"variant":"PT02","link":"https:\/\/m.media-amazon.com\/images\/I\/715M80emuTL.jpg","height":1000,"width":1000},{"variant":"PT02","link":"https:\/\/m.media-amazon.com\/images\/I\/616EHH0o7YL.jpg","height":500,"width":500},{"variant":"PT02","link":"https:\/\/m.media-amazon.com\/images\/I\/616EHH0o7YL._SL75_.jpg","height":75,"width":75}]}]');
    $imagedata = json_decode($data, true);
    po($imagedata);
    // exit;
    if (isset($imagedata[0]['images'])) {

        foreach ($imagedata[0]['images'] as $counter => $image_data_new) {
            $counter++;

            if (array_key_exists("link", $image_data_new)) {
                $img1["Images${counter}"] = '';
                if ($counter == 1) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                } else if ($counter == 4) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                } else if ($counter == 7) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                } else if ($counter == 10) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                } else if ($counter == 13) {
                    ($img1["Images${counter}"] = $image_data_new['link']);
                }
            }
        }
    } else {
        for ($i = 1; $i <= 5; $i++) {
            $img1["Images${i}"] = '';
        }
    }

    po($img1);
});
//generic Keywords fetch
Route::get('sanju/test/generic_key', function () {
    $asin = [
        'TB0721C6JC3',
        'TB071H1VQCY',
        'TB071GZPPQ4',
        'TB071GYSJ1F',
        'TB071GYSJ1F',

    ];
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

        if (isset($data['attributes'])) {
            $genric_key = json_decode($data['attributes'], true);


            if (isset($genric_key['generic_keyword']) && !empty($genric_key['generic_keyword'])) {

                $generic_array = $genric_key['generic_keyword'];

                foreach ($generic_array as $key => $val) {

                    $generic_keywords[$data['asin']][] = $val['value'];
                }
            }
        }
    }
    po($generic_keywords);
});

//zajel POC
Route::get('sanju/zajel/tracking', function () {

    $awb = "Z6430506";
    $requestUrl = "https://app.shipsy.in/api/customer/integration/consignment/track?reference_number=$awb";
    $api_key = 'a80517c76ae63a0dc191df8484b24d';

    //with HTTP
    $response = Http::withHeaders([
        'api-key' => $api_key,
    ])->get($requestUrl);

    $reference_number = '';
    $status = '';
    if ($response->successful()) {
        $datas = $response->json();
        po($datas);
        exit;


        $reference_number = ($datas['reference_number']);
        $status = ($datas['status']);
        $events = ($datas['events']);
        foreach ($events as $key => $event) {

            $type['type'] = ($event['type']);
            $hub_name['hub_name'] = ($event['hub_name']);
            $customer_update['customer_update'] = ($event['customer_update']);
            $failure_reason['failure_reason'] = ($event['failure_reason']);
            $responce[] = [
                'type' =>  $type['type'],
                'hub_name' => $hub_name['hub_name'],
                'customer_update' => $customer_update['customer_update'],
                'failure_reason' => $failure_reason['failure_reason'],

            ];
        }
    } else {
        $responce[] = [
            'respnse' => 'Invalid Refrence Number or No Details Found',

        ];
    }
    $data = [
        'status' => $status,
        'reference_number' => $reference_number,
        'responce' => $responce,

    ];
    po($data);

    //with curl
    // $curl = curl_init();
    // $headersFS = array(
    //     'api-key:' . $api_key,
    // );
    // curl_setopt($curl, CURLOPT_URL, $requestUrl . '');
    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    // curl_setopt($curl, CURLOPT_HTTPHEADER, $headersFS);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


    // $server_APIoutput = curl_exec($curl);
    // $JsonResponse = json_decode($server_APIoutput);

    // po($JsonResponse);

    // if (curl_errno($curl)) {
    //     echo 'Error:' . curl_error($curl);
    // }

    // curl_close($curl);
});

Route::get('sanju/cns/deletion/logic', function () {
    $files = Storage::files('Cliqnshop\asin_import');
    $DaysBefore = Carbon::now()->subDays(30);

    foreach ($files as $file) {

        $lastModified = Storage::lastModified($file);
        $lastModifiedTime = Carbon::createFromTimestamp($lastModified);

        if ($lastModifiedTime->lt($DaysBefore)) {
            Storage::delete($file);
        }
        po('deleted');
    }
});

Route::get('sanju/db/backup', function () {

    $databaseName = Config::get('database.connections');

    $ignoreArray = [
        'order_no_prefix',
        'buybox',
        'bbstores',
        'aws',
        'b2cship',
        'mongodb',
        'cliqnshop',
        'buybox_stores',
    ];

    foreach ($databaseName as $key => $table) {
        $connections[] = $key;

        $final_connection = array_filter($connections, function ($item) use ($ignoreArray) {
            return (!in_array($item, $ignoreArray));
        });
    }

    foreach ($final_connection as $key => $value) {

        $db_tables[$value] = Schema::connection($value)->getAllTables();
    }
    $web_table = (array) $db_tables['web'];
    $inventory_table = (array)$db_tables['inventory'];
    $order_table = (array) $db_tables['order'];
    $seller_table = (array) $db_tables['seller'];
    $shipntracking_table = (array) $db_tables['shipntracking'];
    $business_table = (array)$db_tables['business'];
    $oms_table = (array)$db_tables['oms'];
    $catalog_table = (array)$db_tables['catalog'];

    $datas = [
        'web_table' => ($db_tables['web']),
        'inventory_table' => ($db_tables['inventory']),
        'order_table' => $db_tables['order'],
        'seller_table' => $db_tables['seller'],
        'shipntracking_table' => $db_tables['shipntracking'],
        'business_table' => $db_tables['business'],
        'oms_table' => $db_tables['oms'],

    ];


    foreach ($web_table as $key => $data) {
        $dat_web['web'][] = $data->Tables_in_mosh_360web;
    }
    foreach ($inventory_table as $key => $inv_data) {
        $data_inv['inventory_table'][] = $inv_data->Tables_in_mosh_inventory;
    }
    foreach ($order_table as $key => $ord_data) {
        $data_ord['order_table'][] = $ord_data->Tables_in_mosh_orders;
    }
    foreach ($seller_table as $key => $sell_data) {
        $data_seller['seller_table'][] = $sell_data->Tables_in_mosh_seller;
    }
    foreach ($shipntracking_table as $key => $ship_data) {
        $data_ship['shipntracking_table'][] = $ship_data->Tables_in_mosh_shipntrack;
    }
    foreach ($business_table as $key => $buis_data) {
        $data_busi['business_table'][] = $buis_data->Tables_in_mosh_business;
    }
    foreach ($oms_table as $key => $oms_data) {
        $data_oms['oms_table'][] = $oms_data->Tables_in_mosh_oms;
    }
    // foreach ($catalog_table as $key => $cat_data) {
    //     $data_cat['catalog_table'][] = $cat_data->Tables_in_mosh_catalog;
    // }
    foreach ($catalog_table as $key => $cat_data) {
        $data_cat['catalog_table'][] = $cat_data->Tables_in_mosh_catalog;
    }
    po($data_cat);
    $table_data = [
        'web' => $dat_web,
        'inventory' => $data_inv,
        'order' => $data_ord,
        'seller' => $data_seller,
        'shipntrack' => $data_ship,
        'business' => $data_busi,
        'oms' => $data_oms,
        'catalog' => $data_cat,
    ];
    // po($table_data);
    $mergedArray = array_merge($dat_web, $data_inv, $data_ord, $data_seller, $data_ship, $data_busi, $data_oms, $data_cat);
    po($mergedArray);
});

Route::get('config/test', function () {
    $datas =  Backup::where("status", 1)->get(["connection", "table_name"])->groupBy("connection");

    // $config = config('database.connections.inventory.dump.excludeTables');
    // dd($config);


    foreach ($datas as $connection => $table_names) {

        $table_names = collect($table_names)->pluck("table_name");

        // Config()->set(
        //     "database.connections.{$connection}.dump.excludeTables",
        //     $table_names
        // );

        $value = Config::get("database.connections.{$connection}.dump.excludeTables");
        po($value);
    }
});

Route::get('sanju/event', function () {
    event(new EventManager('hello world'));
});

Route::get('sanju/test/status', function () {
    $smsa_data =  SmsaTracking::query()
        ->select('activity')
        ->distinct()
        ->get();

    $courier_code =   Courier::query()->where('courier_name', 'SMSA')->select('id')->get();
    $code = $courier_code[0]->id;

    foreach ($smsa_data as $datas) {
        $data = [
            'courier_id' => $code,
            'courier_status' => $datas->activity
        ];
        StatusManagement::upsert($data, ['cp_status_cp_id_unique'], ['courier_id', 'courier_status']);
    }
});

Route::get('sanju/bbcreds/count', function () {

    $sources = ['in', 'us'];
    foreach ($sources as $source) {
        $value = Cache::get('creds_count');
        po($value[$source]);
        // foreach()

    }
    exit;
    $codes = ['in' => '11', 'us' => '4'];

    $counts = [];
    foreach ($codes as $key => $code) {
        $counts[$key] = Aws_credential::query()
            ->where(['mws_region_id' => $code])
            ->selectRaw('count(case when credential_priority = "1" then 1 end) as "1", count(case when credential_priority = "2" then 1 end) as "2",
            count(case when credential_priority = "3" then 1 end) as "3",   count(case when credential_priority = "4" then 1 end) as "4"')
            ->first()->toArray();
    }


    po($counts);
    foreach ($counts['in'] as $key1 => $count) {
        po($key1 + 1);
        po($count);
    }
});

route::get('sanju/feed/test', function () {
    // $feedback_id = 136083019467;
    $feedback_id = 136164019468;
    $store_id = 6;

    $country_code = 'IN';


    $url  = (new FeedOrderDetailsApp360())->getFeedStatus($feedback_id, $store_id, $country_code);

    if ($url) {

        $data = file_get_contents($url);

        $data_json = json_decode(json_encode(simplexml_load_string($data)), true);
        po($data_json);
    }
});


//price and availibility Test
Route::get('price/push', 'SanjayTestController@pricepush')->name('sanjay.test');
Route::get('availability/push', 'SanjayTestController@availability_push')->name('sanjay.availability');
Route::get('price/feed/check/{feed_id}', 'SanjayTestController@feed_check')->name('sanjay.feed.test');

Route::get('test/b-api-catalog/{asin}', 'SanjayTestController@businessapi_catalog')->name('test.busines.api.catalog');

//cliqnshop B-API And Catalog API Limit Increase
Route::get('sanju/test/catalog', function () {
    $display_code = 0;
    $catalogs = [];
    $aws_id = null;
    $seller_id = null;
    $country_code = 'US';
    $siteId = '1.';
    $ignore_key = [];
    $price_conversion_method = 'USATOUAE';
    $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->toArray();
    $token = $mws_regions[0]['aws_verified'][1]['auth_code'];
    $searchKey = 'bottle';
    $productSearchApi = new Search_Product_Request();
    $getProducts = $productSearchApi->getASIN($searchKey, 'key');
    foreach ($getProducts->products as $key => $getProduct) {

        // $productTitle[] = $getProduct->title;
        $ProductPriceRequest = new ProductsRequest();
        $productPrice = $ProductPriceRequest->getASINpr($getProduct->asin);
        $prices = isset($productPrice->includedDataTypes->OFFERS) ? $productPrice->includedDataTypes->OFFERS : '';

        if (isset($prices[0])) {

            $product_asin[] = $getProduct->asin;
            $productPrice1[] = isset($prices[0]->listPrice->value) != '' ? $prices[0]->listPrice->value->amount : $prices[0]->price->value->amount;
        }
    }
    $array_asins = (array_chunk($product_asin, 10));
    $array_prices = (array_chunk($productPrice1, 10));
    foreach ($array_asins as $key => $asin) {
        foreach ($array_prices as $key => $price) {
            if (!is_array($asin)) {
                $asin = array($asin);
            }
            $catalog_for_cliqnshop = new NewCatalog();
            $catalogs = $catalog_for_cliqnshop->FetchDataFromCatalog($asin, $country_code, $seller_id, $token, $aws_id);


            foreach ($catalogs as $key1 => $catalog_data) {
                foreach ($catalog_data as $key2 => $catalog) {

                    if ($key2 == 'attributes') {
                        $attributes = json_decode($catalog);
                        if (array_key_exists('bullet_point', (array) $attributes)) {
                            $catalog_for_cliqnshop[$key1]['short_description'] = isset($attributes->bullet_point[0]->value) ? $attributes->bullet_point[0]->value : '';
                            $long_desc = '';
                            foreach ($attributes->bullet_point as $bullet_point) {
                                $long_desc .= "<p>" . (isset($bullet_point->value) ? $bullet_point->value : '');
                                $catalog_for_cliqnshop[$key1]['long_description'] = $long_desc;
                            }
                        }

                        if (array_key_exists('generic_keyword', (array) $attributes)) {
                            $catalog_for_cliqnshop[$key1]['generic_keyword'] = isset($attributes->generic_keyword[0]->value) ? $attributes->generic_keyword[0]->value : '';
                            $gener_key = [];
                            foreach ($attributes->generic_keyword as $generic) {
                                // $gener_key[] = explode(",", $generic->value);
                                $gener_key[] = preg_split("/[,;]/", $generic->value);
                                $catalog_for_cliqnshop[$key1]['generic_keywords'] = $gener_key;
                            }
                        }
                        if (array_key_exists('item_package_dimensions', (array) $attributes)) {
                            $package_dimensions = json_decode($catalog);
                            $catalog_for_cliqnshop[$key1]['item_package_dimensions'] = isset($package_dimensions->item_package_dimensions) ? $package_dimensions->item_package_dimensions : '';
                        }
                    }

                    if ($key2 == 'browseClassification') {
                        $classifications = json_decode($catalog);
                        if (array_key_exists('classificationId', (array) $classifications)) {
                            $catalog_for_cliqnshop[$key1]['category_code'] = $classifications->classificationId;
                        }
                    }
                    if ($key2 == 'images') {
                        $catalog_images = json_decode($catalog);
                        foreach ($catalog_images[0]->images as $key3 => $images) {


                            if (isset($catalog_images[0]->images)) {
                                if ($key3 <= 9 && $images->height > 500 || $images->width > 500) {
                                    $catalog_for_cliqnshop[$key1]['images'][$catalog_data['asin']]['image' . $key3 + 1] = $images->link;
                                }
                                // if (isset($catalog_images[0]->images)) {
                                //     foreach ($catalog_images[0]->images as  $counter => $image_data_new) {
                                //         $counter++;

                                //         if (isset($image_data_new->link)) {

                                //             $img1["Images${counter}"] = '';
                                //             if ($counter == 1) {
                                //                 ($img1["Images${counter}"] = $image_data_new->link);
                                //             } else if ($counter == 4) {
                                //                 ($img1["Images${counter}"] = $image_data_new->link);
                                //             } else if ($counter == 7) {
                                //                 ($img1["Images${counter}"] = $image_data_new->link);
                                //             } else if ($counter == 10) {
                                //                 ($img1["Images${counter}"] = $image_data_new->link);
                                //             } else if ($counter == 13) {
                                //                 ($img1["Images${counter}"] = $image_data_new->link);
                                //             } else if ($counter == 16) {
                                //                 ($img1["Images${counter}"] = $image_data_new->link);
                                //             } else if ($counter == 19) {
                                //                 ($img1["Images${counter}"] = $image_data_new->link);
                                //             }
                                //         }
                                //         $catalog_for_cliqnshop[$key1]['images'][$catalog_data['asin']] = $img1;
                                //     }
                                // }
                                // if ($key3 <= 10 && $images->height >= 1000 && $images->height <= 2000) {
                                //     $catalog_for_cliqnshop[$key1]['images'][$catalog_data['asin']]['image' . $key3 + 1] = $images->link;
                                // }
                            }
                        }
                    }

                    $catalog_for_cliqnshop[$key1]['asin'] = $catalog_data['asin'];
                    $catalog_for_cliqnshop[$key1]['itemName'] = isset($catalog_data['itemName']) ? $catalog_data['itemName'] : '';
                    $catalog_for_cliqnshop[$key1]['brand'] = isset($catalog_data['brand']) ? $catalog_data['brand'] : '';
                    $catalog_for_cliqnshop[$key1]['color'] = isset($catalog_data['color']) ? $catalog_data['color'] : '';
                    $catalog_for_cliqnshop[$key1]['unit'] = isset($catalog_data['unit']) ? $catalog_data['unit'] : '';
                    $catalog_for_cliqnshop[$key1]['length'] = isset($catalog_data['length']) ? round($catalog_data['length'], 2) : '';
                    $catalog_for_cliqnshop[$key1]['width'] = isset($catalog_data['width']) ? round($catalog_data['width'], 2) : '';
                    $catalog_for_cliqnshop[$key1]['height'] = isset($catalog_data['height']) ? round($catalog_data['height'], 2) : '';
                    // $catalog_for_cliqnshop[$key1]['weight']     = isset($catalog_data['weight']) ? $catalog_data['weight'] : '';
                    // $catalog_for_cliqnshop[$key1]['price_US']      = isset($productPrice1[$key1]) ? $productPrice1[$key1] : '';
                    if (isset($catalog_data['weight'])) {

                        $price_convert = new PriceConversion();
                        $catalog_for_cliqnshop[$key1]['price'] = $price_convert->$price_conversion_method($catalog_data['weight'], $price[$key1]);
                    }
                }
            }

            foreach ($catalog_for_cliqnshop as $cliqnshop_catalog) {

                $obj = $cliqnshop_catalog['item_package_dimensions'] = isset($cliqnshop_catalog['item_package_dimensions']) ? $cliqnshop_catalog['item_package_dimensions'] : '';
                $length_package_dimension =  isset($obj[0]->length->value) ? $obj[0]->length->value : '';
                $width_package_dimension =  isset($obj[0]->width->value) ? $obj[0]->width->value : '';
                $height_package_dimension =  isset($obj[0]->height->value) ? $obj[0]->height->value : '';

                $ignore_cat = DB::connection('cliqnshop')->table('cns_ban_category')->where('site_id', $siteId)->pluck('category_code')->toArray();

                $ignore_brand = DB::connection('cliqnshop')->table('cns_ban_brand')->where('site_id', $siteId)->pluck('brand')->toArray();

                if ($ignore_brand == []) {
                    $ignore_brand = ['Dame', 'Maude'];
                }

                $ignore_brand_for_cliqnshop = ucwords(str_replace(',', '|', implode(',', $ignore_brand)), '|');

                $ignore_asin = DB::connection('cliqnshop')->table('cns_ban_asin')->where('site_id', $siteId)->pluck('asin')->toArray();

                if ($ignore_asin == []) {
                    $ignore_asin = ['B00GGXW720', 'B09JJLQS7S'];
                }

                $ignore_asin_for_cliqnshop = ucwords(str_replace(',', '|', implode(',', $ignore_asin)), '|');

                if (
                    isset($cliqnshop_catalog['price'])
                    && $length_package_dimension !== ''
                    && $length_package_dimension < 25
                    && $width_package_dimension !== ''
                    && $width_package_dimension < 25
                    && $height_package_dimension !== ''
                    && $height_package_dimension < 25
                    && isset($cliqnshop_catalog['images'])
                    && !in_array($cliqnshop_catalog['category_code'], $ignore_cat, true)
                    && preg_match("(" . strtolower($ignore_brand_for_cliqnshop) . ")", strtolower($cliqnshop_catalog['brand'])) !== 1
                    && preg_match("(" . strtolower($ignore_asin_for_cliqnshop) . ")", strtolower($cliqnshop_catalog['asin'])) !== 1
                ) {
                    $category = $cliqnshop_catalog['category_code'] ?? 'demo-new';
                    $asin = $cliqnshop_catalog['asin'];
                    $item_name = $cliqnshop_catalog['itemName'];
                    $brand = $cliqnshop_catalog['brand'];
                    $brand_label = $cliqnshop_catalog['brand'];
                    $color_key = $cliqnshop_catalog['color'];
                    $label = $cliqnshop_catalog['color'];
                    $length_unit = $cliqnshop_catalog['unit'];
                    $length_value = $cliqnshop_catalog['length'];
                    $width_unit = $cliqnshop_catalog['unit'];
                    $width_value = $cliqnshop_catalog['width'];
                    $Price_US_IN = $cliqnshop_catalog['price'];
                    $image_array = $cliqnshop_catalog['images'];
                    $short_description = $cliqnshop_catalog['short_description'] ?? '';
                    $long_description = $cliqnshop_catalog['long_description'] ?? '';
                    $generic_keywords = $cliqnshop_catalog['generic_keywords'] ?? '';
                    $editor = 'cns_search_from_' . 'in';
                    po($asin);
                    $cliqnshop = new CliqnshopCataloginsert();
                    $cliqnshop->insertdata_cliqnshop($siteId, $category, $asin, $item_name, $brand, $brand_label, $color_key, $label, $length_unit, $length_value, $width_unit, $width_value, $Price_US_IN, $image_array, $searchKey, $short_description, $long_description, $generic_keywords, $editor, $display_code);
                } else {
                    Log::notice($cliqnshop_catalog);
                }
            }
        }
    }
});

Route::get('vsb', function () {
    $data = 'test';
    TestZoho::create(['opertaion_type' => 'test', 'api_called_through' => 'test', 'time' => Carbon::now()]);
    // $time = date('h:i A', strtotime(now()));
    // $checkTime = date('Y-m-d H:i:s');;
    po('dd');
    exit;
    $startTime = '06:00 PM';
    $endTime = '06:30 PM';
    $second_start = '07:00 AM';
    $second_end = '08:00 AM';
    $checkTime =  date('h:i A', strtotime(now()));

    $startTimeStamp = strtotime($startTime);
    $endTimeStamp = strtotime($endTime);
    $second_start_stamp = strtotime($second_start);
    $second_end_stamp = strtotime($second_end);
    $checkTimeStamp = strtotime($checkTime);

    $subDays = '2';
    if ($checkTimeStamp >= $startTimeStamp && $checkTimeStamp <= $endTimeStamp ||  $checkTimeStamp >= $second_start_stamp && $checkTimeStamp <= $second_end_stamp) {
        $subDays = '5';
    } else {
        $subDays = getSystemSettingsValue('dump_order_subDays', 5);
    }
    po($subDays);
});
