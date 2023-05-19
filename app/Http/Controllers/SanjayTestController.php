<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Services\test\PriceFeed;
use Illuminate\Support\Facades\DB;
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

    public function businessapi_catalog()
    {
        $asinr = ['B000068O1A', 'B002EROLKM'];
        foreach ($asinr as $asin) {
            try {

                $data = (new ProductSearch())->search_1($asin);
      
                if (isset($data->products[0]->asin)) {
                    $asin = $data->products[0]->asin;
                    $asinType = $data->products[0]->asinType;
                    $signedProductId = $data->products[0]->signedProductId;
                    $offers = json_encode($data->products[0]->includedDataTypes->OFFERS);
                    $images = json_encode($data->products[0]->includedDataTypes->IMAGES);
                    $features = json_encode($data->products[0]->features);
                    $taxonomies = json_encode($data->products[0]->taxonomies);
                    $title = ($data->products[0]->title);
                    $url = ($data->products[0]->url);
                    $bookInformation = json_encode($data->products[0]->bookInformation);
                    $mediaInformation = json_encode($data->products[0]->mediaInformation);
                    $productOverview = json_encode($data->products[0]->productOverview);
                    $productDetails = json_encode($data->products[0]->productDetails);

                    $product_varient_dimensions = json_encode($data->products[0]->productVariations->dimensions);
                    $product_variations = json_encode($data->products[0]->productVariations->variations);
                    $productDescription = json_encode($data->products[0]->productDescription);

                    DB::connection('mongodb')->table('demo')->where('asin', $asin)->update(
                        [
                            'asin' =>      $asin,
                            'title' =>    $title,
                            'url' =>    $url,
                            'images' => $images,
                            'asin_type' => $asinType,
                            'product_description' => $productDescription,
                            'signed_productid' => $signedProductId,
                            'offers' => $offers,
                            'features' => $features,
                            'taxonomies' => $taxonomies,
                            'book_information' =>    $bookInformation,
                            'media_information' =>    $mediaInformation,
                            'product_overview' =>   $productOverview,
                            'product_details' =>   $productDetails,
                            'product_varient_dimensions' =>   $product_varient_dimensions,
                            'product_variations' =>   $product_variations,
                            'created_at' => now()->format('Y-m-d H:i:s'),
                            'updated_at'  => now()->format('Y-m-d H:i:s')
                        ],
                        ["upsert" => true]
                    );
                } else {
                    Log::alert('no data');
                }
            } catch (Exception $e) {

                Log::notice($e);
            }
        }
    }
}
