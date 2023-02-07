<?php

use App\Services\Zoho\ZohoApi;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;

Route::get('mosh/test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');

Route::get('po', function () {

    $order_id = '407-9445619-0363521';

    $item_ids =  OrderItemDetails::query()
        ->select('order_item_identifier', 'asin')
        ->where('amazon_order_identifier', $order_id)
        ->get();

    foreach ($item_ids as $data) {

        $item_id = ($data['order_item_identifier']);



        $zoho = new ZohoApi;
        $zoho_lead_search = $zoho->search($order_id, $item_id);
       
        if (isset($zoho_lead_search['data'][0]['id'])) {

            $lead_id = $zoho_lead_search['data'][0]['id'];

            po($lead_id);
        }
    }

    // }
});
