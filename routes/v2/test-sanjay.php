<?php

use Carbon\Carbon;
use App\Services\Zoho\ZohoApi;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;

Route::get('test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');

Route::get('ta', function () {


    $data = ('[{"marketplaceId":"ATVPDKIKX0DER","images":[{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/61MDJPrqerL.jpg","height":1328,"width":1351},{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/41qMNss2AWL.jpg","height":491,"width":500},{"variant":"MAIN","link":"https:\/\/m.media-amazon.com\/images\/I\/41qMNss2AWL._SL75_.jpg","height":74,"width":75},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/51zJ-EspMHL.jpg","height":1392,"width":1550},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/31zkHA+YvfL.jpg","height":449,"width":500},{"variant":"PT01","link":"https:\/\/m.media-amazon.com\/images\/I\/31zkHA+YvfL._SL75_.jpg","height":67,"width":75}]}]');
    $imagedata = json_decode($data, true);

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
