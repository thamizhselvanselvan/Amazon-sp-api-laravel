<?php

namespace App\Http\Controllers;

use Exception;
use RedBeanPHP\R;
use App\Models\BOE;
use League\Csv\Writer;
use App\Models\Mws_region;
use AWS\CRT\HTTP\Response;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Aws_credential;
use Illuminate\Support\Carbon;
use SellingPartnerApi\Endpoint;
use App\Models\Admin\Ratemaster;
use App\Models\Catalog\pricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogApi;
use App\Services\SP_API\CatalogImport;
use SellingPartnerApi\Api\OrdersV0Api;
use Illuminate\Support\Facades\Storage;
use Illuminate\Cache\RateLimiting\Limit;
use App\Services\Catalog\PriceConversion;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetails;
use App\Services\Zoho\ZohoOrder;
use SellingPartnerApi\Api\CatalogItemsV0Api;
use SellingPartnerApi\Api\ProductPricingApi;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;

class TestController extends Controller
{
  use ConfigTrait;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
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
  }

  public function getSellerOrder($seller_id, $country_code)
  {
    echo "Order Lst";
    //new media new token
    $token = NULL;
    $config = $this->config($seller_id, $country_code, $token);

    $marketplace_ids = $this->marketplace_id($country_code);
    $marketplace_ids = [$marketplace_ids];

    $apiInstance = new OrdersV0Api($config);
    $startTime = Carbon::now()->subDays(1)->toISOString();
    $createdAfter = $startTime;
    $max_results_per_page = 100;

    $next_token = '5EsEGMMJo12aJqJYLDm0ZAmQazDrhw3C6koriEUoNqjGCts1L1KLEUz0v33+eggAxqXyQLkGMBs8VhF73Xgy+6+TtJlDlUR57TLVg9FIRJIw1dq2BZHUh7bozNQEGhbjInTAy+XKVmRZBY+oaVuycwQFure81U/C2uVVFrsVPmp+MNAUdWgftyZHQdPXXp8Uf2GLmUGyr9UGnxD0RJmrryegoU0IPZxXtj51yALnq9+4M6STR6qxShi39nX7sSwDMBO/reDY2s8X+G/WxAkd4Fo++pAnAbakpMzWaPWrWIu7EbcPNB+bB7YS9ceFWcsPA9rNUA+mtRVhjT4sLT8FGGb7MtFtmIzif9BKH8lxMxIxY2fmRzniMupdWe0DnR4g0QZIgYUWbIiLESu+7fZQ8qP+IDCdVkzirToAsYN83sHHM2sVBc8891urusdmQ14zkYuItVQ5UdcoVDSpjSWO78jB0ZZToJ80jb45E6buG/w28uc4Sz+A7JGLiLVUOVHXvyOi2lUk+ruj54GUtHYexbMErlht7x/UfS8yBdUHI8PVgbG4EqTn58E/vfbI0LsGgPYTCOW+z8qPVvvqDB6I8w==';
    // $next_token = iconv("UTF-8", "UTF-8//IGNORE", $next_token);
    $amazon_order_ids = NULL;
    $next_token = NULL;
    $order_statuses = null;
    $order_statuses = ['Unshipped', 'PartiallyShipped', 'Shipped', 'InvoiceUnconfirmed', 'Canceled', 'Unfulfillable'];

    // $order_statuses = ['Pending'];
    $order = $apiInstance->getOrders(
      $marketplace_ids,
      $createdAfter,
      $created_before = null,
      $last_updated_after = null,
      $last_updated_before = null,
      $order_statuses,
      $fulfillment_channels = null,
      $payment_methods = null,
      $buyer_email = null,
      $seller_order_id = null,
      $max_results_per_page,
      $easy_ship_shipment_statuses = null,
      $electronic_invoice_statuses = null,
      $next_token,
      $amazon_order_ids,
      $actual_fulfillment_supply_source_id = null,
      $is_ispu = null,
      $store_chain_store_id = null,
      $data_elements = null
    )->getPayload();
    po($order);
    //
  }

  public function getOrder($order_id, $seller_id, $country_code)
  {
    //new media new token
    $token = NULL;
    // $token = 'Atzr|IwEBIN0kK1fNcVINCWD922Ed_hlgmfFbpJnumV8-L4FctK1RJJvTFx2mXymHIfN3G5TQIGg2lukH-p-fGbTA_7g7h_8SAyfmQVYBq83Gev7WtGagNoDM4mfqAxhOHU-wD3FDyfJomA0P5iAASpb0ecBz72FfmoamkFI4pTbuAwB-G7LjvW-ITkDjgZQl8lnsgCI6J5EN-4e8K9eJrAU5p9LMFjPfk8vTqiRAJx6YKNQvNTtPbm3HXmk3AnoogG44IOVazzad7D0VUOr6KQNSQnmx3aN9R2UBgt67KM2YPugDteKKygm9D0JomfmtlY-f3y0Eox4';

    $config = $this->config($seller_id, $country_code, $token);

    $marketplace_ids = $this->marketplace_id($country_code);
    $marketplace_ids = [$marketplace_ids];

    $apiInstance = new OrdersV0Api($config);
    $startTime = Carbon::now()->subDays(30)->toISOString();
    $createdAfter = $startTime;
    $max_results_per_page = 100;

    // $order_statuses = ['Unshipped', 'PartiallyShipped', 'Shipped', 'InvoiceUnconfirmed', 'Canceled', 'Unfulfillable'];
    $order_statuses = null;
    $next_token = NULL;
    $amazon_order_ids = [$order_id];
    echo '<hr>';
    echo 'Order Details';
    $order = $apiInstance->getOrders($marketplace_ids, $createdAfter, $created_before = null, $last_updated_after = null, $last_updated_before = null, $order_statuses, $fulfillment_channels = null, $payment_methods = null, $buyer_email = null, $seller_order_id = null, $max_results_per_page, $easy_ship_shipment_statuses = null, null, $next_token, $amazon_order_ids, $actual_fulfillment_supply_source_id = null, $is_ispu = null, $store_chain_store_id = null, $data_elements = null)->getPayload();
    po($order);

    echo '<hr>';
    echo 'Order item details';

    $data_element = array('buyerInfo');
    $next_token = NULL;
    $result_orderItems = $apiInstance->getOrderItems($order_id, $next_token, $data_element);

    po($result_orderItems);

    echo '<hr>';
    echo 'Order Address';
    $result_order_address = $apiInstance->getOrderAddress($order_id);

    po($result_order_address);

    exit;
  }

  public function SmsaTracking($awb_no)
  {

    return SmsaTrackingResponse($awb_no);
  }

  public function SmsaBooking()
  {
    $awb_no = '';
    $password = config('database.smsa_password');
    $url = "http://track.smsaexpress.com/SECOM/SMSAwebService.asmx";

    $xmlRequest =
      '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <addShip xmlns="http://track.smsaexpress.com/secom/">
          <passKey>' . $password . '</passKey>
          <refNo>1234567900</refNo>
          <sentDate>22-09-2022</sentDate>
          <idNo>1</idNo>
          <cName>Test name</cName>
          <cntry>India</cntry>
          <cCity>Siwan</cCity>
          <cZip></cZip>
          <cPOBox></cPOBox>
          <cMobile>8585852589</cMobile>
          <cTel1></cTel1>
          <cTel2></cTel2>
          <cAddr1>bhagwanpur</cAddr1>
          <cAddr2>bhagwanpur</cAddr2>
          <shipType>DLV</shipType>
          <PCs>1</PCs>
          <cEmail>test@gmail.com</cEmail>
          <carrValue></carrValue>
          <carrCurr></carrCurr>
          <codAmt>0</codAmt>
          <weight>1</weight>
          <custVal></custVal>
          <custCurr></custCurr>
          <insrAmt></insrAmt>
          <insrCurr></insrCurr>
          <itemDesc></itemDesc>
          <sName>test shipper</sName>
          <sContact>8585852356</sContact>
          <sAddr1>bangalore</sAddr1>
          <sAddr2></sAddr2>
          <sCity>karnatak</sCity>
          <sPhone>8585852356</sPhone>
          <sCntry>India</sCntry>
          <prefDelvDate></prefDelvDate>
          <gpsPoints></gpsPoints>
        </addShip>
      </soap:Body>
    </soap:Envelope>';

    $xmlRequest = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <getPDFSino xmlns="http://track.smsaexpress.com/secom/">
          <awbNo>Bom@7379</awbNo>
          <passKey>290342178314</passKey>
        </getPDFSino>
      </soap:Body>
    </soap:Envelope>';
    $headers = array(
      'Content-type: text/xml',
    );

    $ch = curl_init();
    //setting the curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $data = curl_exec($ch);

    return ($data);
    $plainXML = mungXML(trim($data));
    $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    dd($arrayResult);
    //
  }
  public function BombinoTracking($awb_no)
  {

    //
  }

  public function RenameAmazonInvoice()
  {

    commandExecFunc("mosh:rename-amazon-invoice");
    // dd($data);
    //
  }

  public function GetPricing()
  {

    $source = ['IN' => 39];

    $chunk = 10;
    foreach ($source as $country_code => $seller_id) {

      $calculated_weight = [];
      $country_code_lr = strtolower($country_code);
      if ($country_code_lr == 'in') {

        $this->rate_master_in_ae = GetRateChart('IN-AE');

        $this->rate_master_in_sa = GetRateChart('IN-SA');

        $this->rate_master_in_sg = GetRateChart('IN-SG');
      }

      $product_lp = 'bb_product_lp_seller_detail_' . $country_code_lr . 's';
      $product = 'bb_product_' . $country_code_lr . 's';

      $catalog_table = 'catalog' . $country_code_lr . 's';
      AsinSource::select('AsinSource.asin', "$catalog_table.package_dimensions")
        ->where('AsinSource.source', $country_code)
        ->join($catalog_table, 'AsinSource.asin', '=', "$catalog_table.asin")
        ->chunk($chunk, function ($data) use ($seller_id, $country_code_lr, $product_lp) {

          $pricing = [];
          $asin_details = [];
          $listing_price_amount = '';

          foreach ($data as $value) {
            $a = $value['asin'];
            $calculated_weight[$a] = $this->getWeight($value['package_dimensions']);
            $asin_array[] = "'$a'";
          }

          $asin = implode(',', $asin_array);
          $asin_price = DB::connection('buybox')
            ->select("SELECT PPO.asin,
                GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                group_concat(PPO.listingprice_amount) as listingprice_amount,
                group_concat(PPO.updated_at) as updated_at
                FROM $product_lp as PPO
                    WHERE PPO.asin IN ($asin)
                    GROUP BY PPO.asin
                ");

          foreach ($asin_price as $value) {

            $buybox_winner = explode(',', $value->is_buybox_winner);
            $listing_price = explode(',', $value->listingprice_amount);
            $updated_at = explode(',', $value->updated_at);

            $asin_name = $value->asin;
            $packet_weight = $calculated_weight[$asin_name];

            foreach ($buybox_winner as $key =>  $value1) {

              $price = $country_code_lr . '_price';
              if ($value1 == '1') {

                $listing_price_amount = $listing_price[$key];
                $asin_details =
                  [
                    'asin' =>  $asin_name,
                    'weight' => $packet_weight,
                    $price => $listing_price_amount,
                    'price_updated_at' => $updated_at[$key] ? $updated_at[$key] : NULL,
                  ];
                break 1;
              } else {
                $listing_price_amount =  min($listing_price);
                $asin_details =
                  [
                    'asin' =>  $asin_name,
                    'weight' => $packet_weight,
                    $price => $listing_price_amount,
                    'price_updated_at' => $updated_at[$key] ? $updated_at[$key] : NULL,
                  ];
              }
            }

            if ($country_code_lr == 'in') {

              $price_saudi = $this->INDToSA($packet_weight, $listing_price_amount);
              $price_singapore = $this->INDToSG($packet_weight, $listing_price_amount);
              $price_uae = $this->INDToUAE($packet_weight, $listing_price_amount);

              $destination_price = [
                'uae_sp' => $price_uae,
                'sg_sp' => $price_singapore,
                'sa_sp' => $price_saudi,
              ];
            }
            $pricing[] = [...$asin_details, ...$destination_price];
          }
          pricingIn::upsert($pricing, 'asin_unique', ['asin', 'weight', 'in_price', 'uae_sp', 'sg_sp', 'sa_sp', 'price_updated_at']);
          po($pricing);
          echo "<hr>";
          // exit;
        });
    }
  }

  public function INDToSA($weight, $bb_price)
  {
    return (new PriceConversion())->INDToSA($weight, $bb_price);
  }

  public function INDToSG($weight, $bb_price)
  {
    return (new PriceConversion())->INDToSG($weight, $bb_price);
  }

  public function INDToUAE($weight, $bb_price)
  {
    return (new PriceConversion())->INDToUAE($weight, $bb_price);
  }

  public function USAToINDb2c($weight, $bb_price)
  {
    return (new PriceConversion())->USAToINDB2C($weight, $bb_price);
  }

  public function USAToINDb2b($weight, $bb_price)
  {
    return (new PriceConversion())->USAToINDB2B($weight, $bb_price);
  }

  public function getWeight($dimensions)
  {
    $value = (json_decode($dimensions));
    if (isset($value->Weight)) {

      if ($value->Weight->Units == 'pounds') {

        $weight_kg = poundToKg($value->Weight->value);
        return round($weight_kg, 2);
      }
    } else {
      return 0.5;
    }
  }

  public function USATOUAE($weight, $bb_price)
  {
    return (new PriceConversion())->USATOUAE($weight, $bb_price);
  }

  public function USATOSG($weight, $bb_price)
  {
    return (new PriceConversion())->USATOSG($weight, $bb_price);
  }

  public function INToSA($weight, $bb_price)
  {
    return (new PriceConversion())->INDToSA($weight, $bb_price);
  }

  public function testOrderAPI()
  {
    $seller_id = 35;
    $country_code = 'AE';
    $order_id = '404-2296365-0046701';
    // 406-8657142-1805957
    $token = NULL;
    $config = $this->config($seller_id, $country_code, $token);
    $marketplace_ids = $this->marketplace_id($country_code);
    $marketplace_ids = [$marketplace_ids];


    $apiInstance = new OrdersV0Api($config);
    $startTime = Carbon::now()->subDays(30)->toISOString();
    $createdAfter = $startTime;
    $max_results_per_page = 100;

    $next_token = NULL;
    $amazon_order_ids = [$order_id];
    echo '<hr>';
    echo 'Order Details';
    $order = $apiInstance->getOrderItems($order_id)->getPayload();
    po($order);
  }

  public function emiratePostTracking()
  {




    //
  }

  public function TestZoho()
  {
    (new ZohoOrder())->getOrderDetails();
  }
  public function TestGetZoho($lead)
  {

    (new ZohoOrder())->zohoOrderDetails($lead);
  }

  public function TestAmazonFeed()
  {

    (new FeedOrderDetails())->FeedOrderTrackingNo();
  }

  public function testFeed()
  {
  }
}
