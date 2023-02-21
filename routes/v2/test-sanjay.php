<?php

use Carbon\Carbon;
use App\Services\Zoho\ZohoApi;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;

Route::get('test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');

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

Route::get('test/cs', function () {
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

                $generic_array= $genric_key['generic_keyword'];

                foreach ($generic_array as $key => $val) {

                    $generic_keywords[$data['asin']][]= $val['value'];
                
                    
                }
            }
        }
    }
    po($generic_keywords);
});
