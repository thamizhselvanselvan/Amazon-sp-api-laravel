<?php

namespace App\Http\Controllers;

use Exception;
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
    $order = config('database.connections.order.database');
    $catalog = config('database.connections.catalog.database');
    $web = config('database.connections.web.database');
echo $web;
    $label = DB::select("SELECT * from $web.labels as web 
      INNER JOIN $order.orders as ord ON ord.amazon_order_identifier = web.order_no 
      INNER JOIN $order.orderitemdetails as ordetail ON ordetail.amazon_order_identifier = ord.amazon_order_identifier
      INNER JOIN $catalog.catalog as cat ON cat.asin = ordetail.asin
    ");
    dd($label);



    exit;
    $catalogImport = new CatalogImport();
    $catalogData = $catalogImport->amazonCatalogImport();


    exit;
    $year = date('Y');
    $month = date('F');

    echo date('d/m/Y');
    exit;
    $pdfParser = new Parser();
    $BOEPDFMaster = [];
    $pdf = $pdfParser->parseFile('D:\laragon\www\amazon-sp-api-laravel\storage\app/US10000433.pdf');
    $content = $pdf->getText();
    $content = preg_split('/[\r\n|\t|,]/', $content, -1, PREG_SPLIT_NO_EMPTY);
    dd($content);
    exit;
    //   $file_path = BOE::first();
    //   $file_path_array = explode('/', $file_path->download_file_path);
    //   // dd($file_path_array);
    //   $file_name = $file_path_array[count($file_path_array) - 1];

    //   $file = storage_path('app/' . $file_path->download_file_path);

    //   if (!Storage::exists($exportFilePath . $this->fileNameOffset . '.csv')) {

    //      Storage::disk('do')->put("BOE/$file_path->download_file_path", '');
    // }

    //   Storage::disk('do')->put("BOE/$file_path->download_file_path", file_get_contents($file));

    //   // dd($file_name);
    //   dd($file_path->download_file_path);



    BOE::where('do', 0)->chunk(5, function ($files_path) use ($year, $month) {

      foreach ($files_path as $file_path) {

        $file_path_array = explode('/', $file_path->download_file_path);
        $file_name = $file_path_array[count($file_path_array) - 1];

        $file = storage_path('app/' . $file_path->download_file_path);
        Storage::disk('do')->put("BOE/boe/$year/$month/$file_name", file_get_contents($file));
        BOE::where('id', $file_path->id)->update(['do' => 1]);
      }
    });

    exit;


    $file_path = BOE::first();
    $file_path = ($file_path['file_path']);
    if (Storage::exists($file_path)) {
      return Storage::download($file_path);
    }

    exit;
    $amazonCoutn = DB::select('select count(*) from amazon ');
    print_r($amazonCoutn);
    echo "<hr>";
    $amazonData = DB::select('select * from amazon');
    foreach ($amazonData as $data) {

      print_r(json_decode(json_encode($data)));
      echo "<hr>";
    }
  }

  public function SellerTest()
  {

    $aws_data = OrderSellerCredentials::where('dump_order', 1)->get();

    foreach ($aws_data as $aws_value) {

      $awsId  = $aws_value['id'];
      $awsCountryCode = $aws_value['country_code'];
      $this->seller_id = $aws_value['seller_id'];
      $bb_aws_cred = Aws_credential::where('seller_id', $this->seller_id)->get();
      $awsAuth_code = $bb_aws_cred[0]->auth_code;

      $config = $this->config($awsId, $awsCountryCode, $awsAuth_code);
      $marketplace_ids = $this->marketplace_id($awsCountryCode);
      $marketplace_ids = [$marketplace_ids];

      $apiInstance = new OrdersApi($config);
      $startTime = Carbon::now()->subMinute(30)->toISOString();
      $createdAfter = $startTime;
      $lastUpdatedBefore = now()->toISOString();
      $max_results_per_page = 100;
      $next_token = NULL;

      try {
        $results = $apiInstance->getOrders($marketplace_ids, $createdAfter, $created_before = null, $last_updated_after = null, $last_updated_before = null, $order_statuses = null, $fulfillment_channels = null, $payment_methods = null, $buyer_email = null, $seller_order_id = null, $max_results_per_page, $easy_ship_shipment_statuses = null, $next_token, $amazon_order_ids = null, $actual_fulfillment_supply_source_id = null, $is_ispu = null, $store_chain_store_id = null, $data_elements = null)->getPayload();
        po($results);
    
        // $next_token = $results['next_token'];
        $orders = '';
        $amazon_order_id = '';
      } catch (Exception $e) {

        Log::warning('Exception when calling OrdersApi->getOrders: ', $e->getMessage(), PHP_EOL);
      }
    }
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
