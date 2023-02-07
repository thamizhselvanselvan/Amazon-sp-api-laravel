<?php

use Carbon\Carbon;
use App\Services\Zoho\ZohoApi;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;

Route::get('mosh/test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');

Route::get('po', function () {

    $order_id = '405-4525180-0684340';

    $item_ids[] =  '44587226158675';	

    // $item_ids =  OrderItemDetails::query()
    //     ->select('order_item_identifier', 'asin')
    //     ->where('amazon_order_identifier', $order_id)
    //     ->get();

    foreach ($item_ids as $data) {

        // $item_id = ($data['order_item_identifier']);
        $item_id = $data;

        $zoho = new ZohoApi;
        $zoho_lead_search = $zoho->search($order_id, $item_id);
    //    po($zoho_lead_search);
        if (isset($zoho_lead_search['data'][0]['id'])) {

            $lead_id = $zoho_lead_search['data'][0]['id'];
            //   $val =date('d-m-Y',strtotime('2023-02-16T19:59:59Z'));
            $val = Carbon::parse('2023-02-20T19:59:59Z')->format('Y-m-d');
          
            $zoho->updateLead($lead_id, ["US_EDD" => $val]);
            po($zoho);
        }
    }

    // }
});
