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
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
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

    // $next_token = '5EsEGMMJo12aJqJYLDm0ZAmQazDrhw3C6koriEUoNqjGCts1L1KLEUz0v33+eggAxqXyQLkGMBs8VhF73Xgy+6+TtJlDlUR57TLVg9FIRJIw1dq2BZHUh7bozNQEGhbjInTAy+XKVmRZBY+oaVuycwQFure81U/C2uVVFrsVPmp+MNAUdWgftyZHQdPXXp8Uf2GLmUGyr9UGnxD0RJmrryegoU0IPZxXtj51yALnq9+4M6STR6qxShi39nX7sSwDMBO/reDY2s8X+G/WxAkd4Fo++pAnAbakpMzWaPWrWIu7EbcPNB+bB7YS9ceFWcsPA9rNUA+mtRVhjT4sLT8FGGb7MtFtmIzif9BKH8lxMxIxY2fmRzniMupdWe0DnR4g0QZIgYUWbIiLESu+7fZQ8qP+IDCdVkzirToAsYN83sHHM2sVBc8891urusdmQ14zkYuItVQ5UdcoVDSpjSWO78jB0ZZToJ80jb45E6buG/w28uc4Sz+A7JGLiLVUOVHXvyOi2lUk+ruj54GUtHYexbMErlht7x/UfS8yBdUHI8PVgbG4EqTn58E/vfbI0LsGgPYTCOW+z8qPVvvqDB6I8w==';
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
    $config = $this->config($seller_id, $country_code, $token);

    $marketplace_ids = $this->marketplace_id($country_code);
    $marketplace_ids = [$marketplace_ids];

    $apiInstance = new OrdersV0Api($config);
    $startTime = Carbon::now()->subDays(1)->toISOString();
    $createdAfter = $startTime;
    $max_results_per_page = 100;

    // $order_statuses = ['Unshipped', 'PartiallyShipped', 'Shipped', 'InvoiceUnconfirmed', 'Canceled', 'Unfulfillable'];
    $order_statuses = null;
    $next_token = NULL;
    $amazon_order_ids = [$order_id];
    try {

      echo '<hr>';
      echo 'Order Details';
      echo '<br>';
      $order = $apiInstance->getOrders($marketplace_ids, $createdAfter, $created_before = null, $last_updated_after = null, $last_updated_before = null, $order_statuses, $fulfillment_channels = null, $payment_methods = null, $buyer_email = null, $seller_order_id = null, $max_results_per_page, $easy_ship_shipment_statuses = null, null, $next_token, $amazon_order_ids, $actual_fulfillment_supply_source_id = null, $is_ispu = null, $store_chain_store_id = null, $data_elements = null);
      $request_id = $order['headers']['x-amzn-RequestId'];
      echo "Request Id: " . $request_id[0];
      po($order->getPayload());
    } catch (Exception $e) {
      po($e);
    }

    echo '<hr>';
    echo 'Order item details';
    echo "<br>";
    try {

      $data_element = array('buyerInfo');
      $next_token = NULL;
      $result_orderItems = $apiInstance->getOrderItems($order_id, $next_token, $data_element);
      $request_id = $result_orderItems['headers']['x-amzn-RequestId'];
      echo "Request Id: " . $request_id[0];
      po($result_orderItems->getPayload());
    } catch (Exception $e) {

      echo "<br>";
      echo 'Request Id: ' . (($e->getResponseHeaders())['x-amzn-RequestId'][0]);
      echo "<br>";

      po($e);
    }
    // exit;
    echo '<hr>';
    echo 'Order Address';
    try {

      $result_order_address = $apiInstance->getOrderAddress($order_id);
      $request_id = $result_order_address['headers']['x-amzn-RequestId'];

      echo "Request Id: " . $request_id[0];

      po($result_order_address->getPayload());
    } catch (Exception $e) {
      echo "<br>";
      echo 'Request Id: ' . (($e->getResponseHeaders())['x-amzn-RequestId'][0]);
      echo "<br>";

      po($e);
    }

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

  public function emiratePostTracking($tracking_id)
  {
    $account_no = 'C175120';
    $password = 'C175120';

    $tracking_id = '123456783';
    $curl = curl_init();

    $headers = array(
      'Content-Type: text/xml; charset=utf-8',
      'SOAPAction: http://epg.generic.tracking/TrackShipmentByAwbNo',
      'AccountNo: ' . $account_no,
      'Password: ' . $password
    );

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://osbtest.epg.gov.ae/ebs/genericapi/tracking',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <TrackShipmentByAwbNo xmlns="http://epg.generic.tracking/">
          <AwbNo>' . $tracking_id . '</AwbNo>
          <ShipmentType>Standard</ShipmentType>
        </TrackShipmentByAwbNo>
      </soap:Body>
    </soap:Envelope>
    ',
      CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $result = json_decode(json_encode($response));
    $plainXML = mungXML($result);
    $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    dd($arrayResult);
  }

  public function emiratePostBooking()
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://osbtest.epg.gov.ae/ebs/genericapi/booking',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '<?xml version="1.0" encoding="utf-8"?>
      <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
          <CreateBookingRequest xmlns="http://epg.generic.booking/">
            <BookingRequest>
              <SenderContactName>Amit Singh</SenderContactName>
              <SenderCompanyName>Mosh</SenderCompanyName>
              <SenderAddress>Warehouse61</SenderAddress>
              <SenderCity>Dubai</SenderCity>
              <SenderContactMobile></SenderContactMobile>
              <SenderContactPhone></SenderContactPhone>
              <SenderEmail>Test@moshgmail.com</SenderEmail>
              <SenderZipCode></SenderZipCode>
              <SenderState>Dubai</SenderState>
              <SenderCountry>UAE</SenderCountry>
              <ReceiverContactName>Test Amit</ReceiverContactName>
              <ReceiverCompanyName>Mosh</ReceiverCompanyName>
              <ReceiverAddress>flat no 313</ReceiverAddress>
              <ReceiverCity>Ajman</ReceiverCity>
              <ReceiverCityName>Ajman</ReceiverCityName>
              <ReceiverContactMobile></ReceiverContactMobile>
              <ReceiverContactPhone></ReceiverContactPhone>
              <ReceiverEmail>Aramex@gmail.com</ReceiverEmail>
              <ReceiverZipCode></ReceiverZipCode>
              <ReceiverState>Ajman</ReceiverState>
              <ReceiverCountry>Dubai</ReceiverCountry>
              <ReferenceNo>405-1952257037126</ReferenceNo>
              <ReferenceNo1>1</ReferenceNo1>
              <ReferenceNo2>2</ReferenceNo2>
              <ReferenceNo3>3</ReferenceNo3>
              <ContentTypeCode>4</ContentTypeCode>
              <NatureType>NA</NatureType>
              <Service>Domestic</Service>
              <ShipmentType>Premium</ShipmentType>
              <DeleiveryType>Counter</DeleiveryType>
              <Registered>No</Registered>
              <PaymentType>COD</PaymentType>
              <CODAmount>100</CODAmount>
              <CODCurrency>AED</CODCurrency>
              <CommodityDescription>string</CommodityDescription>
              <Pieces>1</Pieces>
              <Weight>100</Weight>
              <WeightUnit>Grams</WeightUnit>
              <Length>.5</Length>
              <Width>.5</Width>
              <Height>1</Height>
              <DimensionUnit>Meter</DimensionUnit>
              <ItemValue>string</ItemValue>
              <ValueCurrency>string</ValueCurrency>
              <ProductCode>string</ProductCode>
              <SpecialInstructionsID>string</SpecialInstructionsID>
              <DeliveryInstructionsID>string</DeliveryInstructionsID>
              <HandlingInstructionsID>string</HandlingInstructionsID>
              <LabelType>RPT</LabelType>
              <RequestSource>string</RequestSource>
              <isReturnItem>No</isReturnItem>
              <SendMailToSender>No</SendMailToSender>
              <SendMailToReceiver>No</SendMailToReceiver>
              <CustomDeclarations>
                <CustomDeclarationRequest>
                  <HSCode>1</HSCode>
                  <TotalUnits>100</TotalUnits>
                  <Weight>100</Weight>
                  <Value>39</Value>
                  <DeclaredCurrency>AED</DeclaredCurrency>
                  <FileName></FileName>
                  <FileType></FileType>
                  <FileContent></FileContent>
                  <CreatedBy>Amit</CreatedBy>
                  <CategoryCode></CategoryCode>
                  <Category></Category>
                  <Description></Description>
                </CustomDeclarationRequest>
              </CustomDeclarations>
              <MailCategoryID>100</MailCategoryID>
              <PreferredPickupDate>2022-09-29T11:11:22.715Z</PreferredPickupDate>
              <PreferredPickupTimeFrom>2022-09-29T01:11:22.715Z</PreferredPickupTimeFrom>
              <PreferredPickupTimeTo>2022-09-29T23:11:22.715Z</PreferredPickupTimeTo>
              <PrintType>LabelOnly</PrintType>
              <AWBType>EAWB</AWBType>
              <Is_Return_Service>No</Is_Return_Service>
              <Latitude></Latitude>
              <Longitude></Longitude>
              <IsOnlinePayment>No</IsOnlinePayment>
              <TransactionSource></TransactionSource>
              <RequestType>Booking</RequestType>
              <ReturnItemCode></ReturnItemCode>
              <SenderCountryID></SenderCountryID>
              <SenderZone></SenderZone>
              <SenderRegion></SenderRegion>
              <PrefereredDateTimeFrom>2022-09-29T11:11:22.715Z</PrefereredDateTimeFrom>
              <PrefereredDateTimeTo>2022-09-29T11:11:22.715Z</PrefereredDateTimeTo>
              <IsDropOff>No</IsDropOff>
              <DropOffOfficeId></DropOffOfficeId>
              <Remarks></Remarks>
              <SpecialNotes></SpecialNotes>
              <VehicleTypeCode></VehicleTypeCode>
              <DeliveryLatitude></DeliveryLatitude>
              <DeliveryLongitude></DeliveryLongitude>
              <SenderRegionId></SenderRegionId>
              <ReceiverRegionId></ReceiverRegionId>
              <AWBNumber></AWBNumber>
              <ReceiverLevel1ID>1</ReceiverLevel1ID>
              <ReceiverLevel1Name></ReceiverLevel1Name>
              <ReceiverLastLevelID></ReceiverLastLevelID>
              <ReceiverLastLevelName></ReceiverLastLevelName>
              <ConsignmentPiecesInfo>
                <ConsignmentPiecesInfoBO>
                  <Weight>1</Weight>
                  <Length>1</Length>
                  <Width>1</Width>
                  <Height>1</Height>
                </ConsignmentPiecesInfoBO>
              </ConsignmentPiecesInfo>
              <ActualPrice>100</ActualPrice>
              <DiscountAmount></DiscountAmount>
              <DiscountFlag></DiscountFlag>
              <DiscountPercent></DiscountPercent>
              <DiscountPrice></DiscountPrice>
              <EmployeeEmail></EmployeeEmail>
              <EmployeeId></EmployeeId>
              <EmployeeMobile></EmployeeMobile>
              <IsPudoDelivery></IsPudoDelivery>
              <PudoLocationId></PudoLocationId>
            </BookingRequest>
          </CreateBookingRequest>
        </soap:Body>
      </soap:Envelope>
    ',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: http://epg.generic.booking/CreateBooking',
        'AccountNo: C175120',
        'Password: C175120'
      ),
    ));
    $response = curl_exec($curl);
    $plainXML = mungXML($response);
    $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    dd($arrayResult);

    curl_close($curl);
    dd($response);
    echo $response;

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

  public function TestAmazonFeed($feed_id)
  {

    $country_code = 'IN';

    $config = $this->config(6, $country_code, $token = NULL);
    $apiInstance = new FeedsApi($config);
    $result = ($apiInstance->getFeed($feed_id));

    $result = json_decode(json_encode($result));
    $feed_doc_id = $result->resultFeedDocumentId;

    $doc_result = $apiInstance->getFeedDocument($feed_doc_id);

    $doc_result = json_decode(json_encode($doc_result));
    $url  = $doc_result->url;

    echo "<script> window.open('" . $url . "','_blank')</script>";
    exit;
  }

  public function testFeed()
  {
  }
}
