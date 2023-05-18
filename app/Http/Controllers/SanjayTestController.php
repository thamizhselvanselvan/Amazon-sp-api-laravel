<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Services\test\PriceFeed;
use Illuminate\Support\Facades\Log;
use App\Services\test\AvailabilityFeed;
use App\Support\BusinessAPI\ProductSearch;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\API\CategoryTreeReport;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

class SanjayTestController extends Controller
{

    //Submit Price
    public function pricepush(Request $request)
    {
        if ($request->ajax()) {
            $submit_data =  $request->data;
            $p_sku = $submit_data['product_sku'];
            $price = $submit_data['price'];
            $store_data = $submit_data['store_select'];
            $asin = $submit_data['asin'];

            $s_data = explode("-", $store_data);
            $store_id = $s_data[0];
            $region = $s_data[1];

            $ceil_price = (($price * 20) / 100) + $price;
            $base_price = $price - (($price * 20) / 100);

            $feedLists = [
                "push_price" => $price,
                "store_id" => $store_id,
                "region" => $region,
                "base_price" => $base_price,
                "ceil_price" => $ceil_price,
                "sku" => $p_sku,
            ];

            $price_update = (new PriceFeed)->price_submit($feedLists, $asin);

            Log::alert('Price Feed Id = ' . $price_update);
            $feed_id =  $price_update;
            return response()->json($feed_id);
        }
        $stores =  OrderSellerCredentials::select('store_name', 'country_code', 'seller_id')->where('cred_status', 1)->get();
        return view('test_price', compact('stores'));
    }
    //Submit Availibility
    public function availability_push(Request $request)
    {

        if ($request->ajax()) {
            $submit_data =  $request->data;
            $p_sku = $submit_data['product_sku'];
            $availability = $submit_data['availability'];
            $store_data = $submit_data['store_select'];
            $asin = $submit_data['asin'];

            $s_data = explode("-", $store_data);
            $seller_id = $s_data[0];
            $regionCode = $s_data[1];

            $feedLists[] = [
                'product_sku' => $p_sku,
                'available' => $availability
            ];

            $PushAvailability = new AvailabilityFeed();
            $availability = $PushAvailability->availability_feed($feedLists, $seller_id, $regionCode, $asin, $availability);

            Log::Notice('availibility feedback_id = ' . $availability);
            $feed_id =  $availability;
            return response()->json($feed_id);
        }



        $stores =  OrderSellerCredentials::select('store_name', 'country_code', 'seller_id')->where('cred_status', 1)->get();
        return view('test_availability', compact('stores'));
    }

    //get feedback responce
    public function feed_check(Request $request, $id)
    {
        $data = explode('-', $id);
        $feedback_id = $data[0];
        $store_id = $data[1];
        $country_code = $data[2];

        $url  = (new FeedOrderDetailsApp360())->getFeedStatus($feedback_id, $store_id, $country_code);

        if ($url) {

            $data = file_get_contents($url);

            $data_json = json_decode(json_encode(simplexml_load_string($data)), true);
            po($data_json);
        }
        return view('test_feed');
    }

    public function businessapi_catalog($asin)
    {
        // $asin = 'B000068O1A';
        $data = (new ProductSearch())->search_1($asin);
        po($data);
    }
}
