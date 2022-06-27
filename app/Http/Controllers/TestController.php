<?php

namespace App\Http\Controllers;

use Exception;
use RedBeanPHP\R;
use App\Models\BOE;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Aws_credential;
use Illuminate\Support\Carbon;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Config\ConfigTrait;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogApi;
use App\Services\SP_API\CatalogImport;
use Illuminate\Support\Facades\Storage;
use App\Models\order\OrderSellerCredentials;
use SellingPartnerApi\Api\CatalogItemsV0Api;
use SellingPartnerApi\Api\ProductPricingApi;

class TestController extends Controller
{
  use ConfigTrait;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $token = 'Atzr|IwEBIPB-bwH72vziBxC_yS2L95g1g5YHzk2uFK5XVfDN81DNCWqZ_P7tB_AZnW_sTbc4ksR2j9qag1v4lBByY_ujE3gulLdRhNRQ37ztoLaIAGkthEE2GP16y4QQPNdJ0teD0HGZQ8gjX62XWBFPMDZNMuErOLxu3s3pB_GmaGS54TSljnLmNdxAvmVOW63c3N79QdO_Bg91UqaTVUGiSgbAO8P5ebDlQON1OlXZzQzR-yMcApXmhn8mVuN5U9aCKeXa8bg8pH0FSvK6LE17Vig-_Tg5AFa-7dOTe9CO3uoLWBZyaqy0aRinqFc1XBMKQAs0uII';
    //AE
    $marketplace = 'A2VIGQ35RCS4UG';
    $endpoint = Endpoint::EU;

    $config = new Configuration([
      "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
      "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
      "lwaRefreshToken" => $token,
      "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
      "awsSecretAccessKey" => "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",
      "endpoint" => $endpoint,  // or another endpoint from lib/Endpoints.php
      "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
    ]);

    $apiInstance = new OrdersApi($config);
    $order_id = '406-2019809-3971536';

    $next_token = NULL;
    $data_element = array('buyerInfo');
    $result_orderItems = $apiInstance->getOrderItems($order_id, $next_token, $data_element);
    $result_order_address = $apiInstance->getOrderAddress($order_id);
    po($result_order_address);
    echo "<hr>";
    po($result_orderItems);
    exit;
    dd($result_order_address, $result_orderItems);
    exit;
    $order = config('database.connections.order.database');
    $catalog = config('database.connections.catalog.database');
    $web = config('database.connections.web.database');

    $label = DB::select("SELECT * from $web.labels as web 
      INNER JOIN $order.orders as ord ON ord.amazon_order_identifier = web.order_no 
      INNER JOIN $order.orderitemdetails as ordetail ON ordetail.amazon_order_identifier = ord.amazon_order_identifier
      INNER JOIN $catalog.catalog as cat ON cat.asin = ordetail.asin
    ");
    dd($label);
  }

  public function SellerTest()
  {
    $host = config('database.connections.order.host');
    $dbname = config('database.connections.order.database');
    $port = config('database.connections.order.port');
    $username = config('database.connections.order.username');
    $password = config('database.connections.order.password');

    R::addDatabase('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    R::selectDatabase('order');

    $aws_data = OrderSellerCredentials::where('seller_id', 9)->get();
    // dd($aws_data);

    foreach ($aws_data as $aws_value) {

      $awsId  = $aws_value['id'];
      $awsCountryCode = $aws_value['country_code'];
      $this->seller_id = $aws_value['seller_id'];
      $bb_aws_cred = Aws_credential::where('seller_id', 9)->get();
      $awsAuth_code = $bb_aws_cred[0]->auth_code;

      $config = $this->config($awsId, $awsCountryCode, $awsAuth_code);
      $marketplace_ids = $this->marketplace_id($awsCountryCode);
      $marketplace_ids = [$marketplace_ids];

      $apiInstance = new OrdersApi($config);
      // $startTime = Carbon::now()->subMinute(30)->toISOString();
      // $createdAfter = $startTime;
      // $lastUpdatedBefore = now()->toISOString();
      // $max_results_per_page = 100;
      // $next_token = NULL;
      $order_id = '408-8883245-7772314';
      // $order_id = '403-6898279-3539565';
      $next_token = NULL;
      $data_element = array('buyerInfo');
      try {
        // $results = $apiInstance->getOrders($marketplace_ids, $createdAfter, $created_before = null, $last_updated_after = null, $last_updated_before = null, $order_statuses = null, $fulfillment_channels = null, $payment_methods = null, $buyer_email = null, $seller_order_id = null, $max_results_per_page, $easy_ship_shipment_statuses = null, $next_token, $amazon_order_ids = null, $actual_fulfillment_supply_source_id = null, $is_ispu = null, $store_chain_store_id = null, $data_elements = null)->getPayload();
        $result_orderItems = $apiInstance->getOrderItems($order_id, $next_token, $data_element);
        $result_order_address = $apiInstance->getOrderAddress($order_id);
        $seller_id = 9;
        // $this->OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $seller_id, $awsCountryCode);
        po($result_order_address);
        exit;
        // po($result_orderItems['payload']['order_items'] );
        // exit;
        // $next_token = $results['next_token'];
        $orders = '';
        $amazon_order_id = '';
      } catch (Exception $e) {
        echo $e->getMessage();
        // exit;
        // Log::warning('Exception when calling OrdersApi->getOrders: ', $e->getMessage(), PHP_EOL);
      }
      // exit;
    }
  }


  public function OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $seller_id, $awsCountryCode)
  {
    $result_order_address = (array)$result_order_address;
    foreach ($result_order_address as $result_address) {
      foreach ((array)$result_address['payload'] as $result) {
        $count = 0;
        foreach ($result as $key => $value) {

          $detailsKey = lcfirst($key);
          $id = substr($detailsKey, -2);
          $ids = substr($detailsKey, -3);
          // echo $id;
          if ($id == 'id' || $id == 'Id' || $ids == 'ids') {
            $detailsKey = str_replace(["id", 'Id', 'ids'], "identifier", $detailsKey);
          }

          if (is_array($value) || is_object($value)) {
            // $order_detials->$detailsKey = json_encode($value);
            $order_address = json_encode($value);
          } else {
            $count = 1;
            // $order_detials->$detailsKey = $value;
            $amazon_order = $value;
          }
        }
      }
    }

    foreach ($result_orderItems['payload']['order_items'] as $result_order) {
      foreach ((array)$result_order as $result) {
        $order_detials = R::dispense('orderitemdetailstest');
        $order_detials->seller_identifier = $seller_id;
        $order_detials->status = '0';
        $order_detials->country = $awsCountryCode;

        foreach ($result as $key => $value) {
          $detailsKey = lcfirst($key);
          $id = substr($detailsKey, -2);
          $ids = substr($detailsKey, -3);
          // echo $id;
          if ($id == 'id' || $id == 'Id' || $ids == 'ids') {
            $detailsKey = str_replace(["id", 'Id', 'ids'], "identifier", $detailsKey);
          }

          if (is_array($value)) {

            $order_detials->{$detailsKey} = json_encode($value);
          } elseif (is_object(($value))) {
            $order_detials->{$detailsKey} = json_encode($value);
          } else {
            $order_detials->{$detailsKey} = ($value);
          }
        }
        $order_detials->amazon_order_identifier = $amazon_order;
        $order_detials->shipping_address = $order_address;
        R::store($order_detials);
      }
    }
    // DB::connection('order')
    // ->update("UPDATE orders SET order_item = '1' where amazon_order_identifier = '$order_id'");
  }


  public function getASIN($asin, $country_code)
  {


    $asins = array($asin);
    $token = '';
    $marketplace = '';
    $endpoint = '';

    switch ($country_code) {
      case 'US':
      case 'us':
        //us Token
        $token = "Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
        $marketplace = 'ATVPDKIKX0DER'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
        $endpoint = Endpoint::NA;
        break;

      case 'IN':
      case 'in':
        //india Token
        $token = "Atzr|IwEBIJbccmvWhc6q6XrigE6ja7nyYj962XdxoK8AHhgYvfi-WKo3MsrbTSLWFo79My_xmmT48DSVh2e_6w8nxgaeza9XZ9HtNnk7l4Rl_nWhhO6xzEdfIfU7Ev4hktjvU8CjMvYnRn_Cw5JveEqZSggp961Sg7CoBEDpwXZbAE3SYXSdeNxfP2Nu84y2ZzlsP3CNZqcTvXMWflLk1qqY6ittwlGAXpL0BwGxPCBRmjbXOy5xsZqwCPAQhW6l9AJtLPhwOlSSDjcxxvCTH9-LEPSWHLRP1wV3fRgosOlCsQgmuET0pm5SO7FVJTRWux8h2k5hnnM";
        $marketplace = 'A21TJRUUN4KGV'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned. 
        $endpoint = Endpoint::EU;
        break;

      case 'SA':
      case 'sa':
        // Saudi Arabia
        $token = '';
        $marketplace = 'A17E79C6D8DWNP';
        $endpoint = Endpoint::EU;
        break;

      case 'AE':
      case 'ae':
        //UAE
        $token = 'Atzr|IwEBIHB8x1yx3bRdnjOICk1qxFMYPczPiS9NGCHm6M-f6SLwHbzaehUZz7mWKRNddG5LFo4ZB00DdONe3u8udOBOR6X6GtJug36YXJeFMIvU7t-2-DJMZ-1PjOBi6U6ubuaAOa2jottylPzVsvKpht6DbTu3rvKtziVq338I8wUV2PnMRPCfc6cM8_9PAQLNVGBBCHiRevHxh9_gsjKCNFEexQD3gQrZPTE5yXnhWkPRv_dSmRdty1P1gmDDK6G8OyotfabU8C_L9ujIIVz13m6Go9eCalMkO_EVtHwTDDICusjxiA26JRbk7qRmPzNL7iiCocY';
        $marketplace = 'A2VIGQ35RCS4UG';
        $endpoint = Endpoint::EU;
        break;
    }

    //$usa_token="Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
    $config = new Configuration([
      "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
      "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
      "lwaRefreshToken" => $token,
      "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
      "awsSecretAccessKey" => "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",
      "endpoint" => $endpoint,  // or another endpoint from lib/Endpoints.php
      "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
    ]);


    $item_type = 'Asin'; // string | Indicates whether ASIN values or seller SKU values are used to identify items. If you specify Asin, the information in the response will be dependent on the list of Asins you provide in the Asins parameter. If you specify Sku, the information in the response will be dependent on the list of Skus you provide in the Skus parameter.
    $skus = array(); // string[] | A list of up to twenty seller SKU values used to identify items in the given marketplace.
    $item_condition = 'New'; // string | Filters the offer listings based on item condition. Possible values: New, Used, Collectible, Refurbished, Club.
    $offer_type = 'B2C'; // string | Indicates whether to request pricing information for the seller's B2C or B2B offers. Default is B2C.

    echo 'Catalog Items API v2020-12-01/ getCatalogItem';
    echo "<hr>";
    $apiInstance = new CatalogApi($config);
    echo "<pre>";

    try {
      $result = $apiInstance->getCatalogItem($asin, $marketplace);
      $result = json_decode(json_encode($result));
      po($result->summaries[0]->itemName);
    } catch (Exception $e) {
      echo 'Exception when calling CatalogApi->getCatalogItem: ', $e->getMessage(), PHP_EOL;
    }

    echo 'Product Pricing Api / getCompetitivePricing';
    echo "<hr>";

    exit;
    $apiInstance = new ProductPricingApi($config);
    try {
      $result = $apiInstance->getCompetitivePricing($marketplace, $item_type, $asins)->getPayload();
      $result = json_decode(json_encode($result));
      po($result);
      echo 'landed price';
      $pricing = $result[0]->Product->CompetitivePricing->CompetitivePrices[0]->Price->LandedPrice;
      print_r($pricing->CurrencyCode);
      print_r($pricing->Amount);
      //   $result = (array)($result->payload->AttributeSets[0]);
    } catch (Exception $e) {
      echo 'Exception when calling ProductPricingApi->getCompetitivePricing: ', $e->getMessage(), PHP_EOL;
    }

    echo "<hr>";
    echo 'Product Pricing Api / getItemOffers';
    echo "<hr>";
    echo "<pre>";
    try {
      $result = $apiInstance->getItemOffers($marketplace, $item_condition, $asin)->getPayload();
      $result = json_decode(json_encode($result));
      print_r($result);
    } catch (Exception $e) {
      echo 'Exception when calling ProductPricingApi->getItemOffers: ', $e->getMessage(), PHP_EOL;
    }

    echo "<hr>";
    echo 'Product Pricing Api / getPricing';
    echo "<hr>";

    try {
      $result = $apiInstance->getPricing($marketplace, $item_type, $asins)->getPayload();
      // po($result);
      $result = json_decode(json_encode($result));
      print_r($result);
    } catch (Exception $e) {
      echo 'Exception when calling ProductPricingApi->getPricing: ', $e->getMessage(), PHP_EOL;
    }





    echo "<hr>";

    echo "<hr>";
    echo 'Product Pricing Api / getListingOffers';
    echo "<hr>";

    try {
      $result = $apiInstance->getListingOffers($marketplace, $item_type, $asins)->getPayload();
      // po($result);
      $result = json_decode(json_encode($result));
      print_r($result);
    } catch (Exception $e) {
      echo 'Exception when calling ProductPricingApi->getPricing: ', $e->getMessage(), PHP_EOL;
    }
  }
}
