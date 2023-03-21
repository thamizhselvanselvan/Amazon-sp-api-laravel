<?php

namespace App\Http\Controllers;

use Exception;
use RedBeanPHP\R;
use App\Models\BOE;
use GuzzleHttp\Client;
use League\Csv\Writer;
use App\Models\Mws_region;
use AWS\CRT\HTTP\Response;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Aws_credential;
use Illuminate\Support\Carbon;
use SellingPartnerApi\Endpoint;
use App\Models\Admin\Ratemaster;
use App\Services\Zoho\ZohoOrder;
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
use GuzzleHttp\Psr7\Request as Http_request;
use SellingPartnerApi\Api\CatalogItemsV0Api;
use SellingPartnerApi\Api\ProductPricingApi;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;
use App\Services\ShipNTrack\Tracking\AramexTracking;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use App\Services\ShipNTrack\Tracking\AramexTrackingServices;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetails;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

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
        $token = "Atzr|IwEBILPcGgwzaEhguZhYhbzfSY700dEmu8kFh8ZinfGqeCC-8aZjzDkj7z53HgfMZ8cJTkk4_7K41_xtEKo_BgWgXPPpRc0KW3rIpTT-Apoz1oOwcE6srNpQRRhGY2KXFJPbBswDWCswKHRcNLFgc3nqcqUqW-p3wu28LcKvdM7CePva8105hvZzr9Csw2pYml141SaVnsxUcdSP8996M0mhwekb_Dtl9kct-6R7TxSFtfsu5NrpxUw58pqyrVKVt1nOWCT6YHLMPdNAye6OF2M6nP2praD1X4gapufEmZypS-OheBCAwXonOqZpz9O4w2DEXyE";
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

    $config = new Configuration([
      "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
      "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
      "lwaRefreshToken" => $token,
      "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
      "awsSecretAccessKey" => "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",
      "endpoint" => $endpoint,  // or another endpoint from lib/Endpoints.php
      "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
    ]);
    $apiInstance = new CatalogItemsV20220401Api($config);

    $includedData = ['attributes', 'dimensions', 'identifiers', 'relationships', 'salesRanks', 'images', 'productTypes', 'summaries'];
    $result = $apiInstance->searchCatalogItems(
      [$marketplace],
      $asins,
      'ASIN',
      $includedData,
    );


    po($result);
    exit;
    //
  }

  public function getSellerOrder($seller_id, $country_code)
  {
    echo "Order List";
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
    $startTime = Carbon::now()->subDays(3)->toISOString();
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
                <PreferredPickupDate>2022-11-04T11:36:37.808+04:00</PreferredPickupDate>
                <PreferredPickupTimeFrom>2022-11-04T11:36:37.808+04:00</PreferredPickupTimeFrom>
                <PreferredPickupTimeTo>2022-11-04T11:36:37.808+04:00</PreferredPickupTimeTo>
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
                <PrefereredDateTimeFrom>2022-10-04T11:36:37.808+04:00<</PrefereredDateTimeFrom>
                <PrefereredDateTimeTo>2022-10-04T11:36:37.808+04:00<</PrefereredDateTimeTo>
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
        </soap:Envelope>',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: http://epg.generic.booking/CreateBooking',
        'AccountNo: C175120',
        'Password: C175120'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
    dd($response);


    //
  }

  public function TestAmazonFeed($feed_id, $seller_id)
  {
    $url  = (new FeedOrderDetailsApp360())->getFeedStatus($feed_id, $seller_id);

    echo "<script> window.open('" . $url . "','_blank')</script>";
    exit;
  }

  public function AramexTracking($tracking_id)
  {
    // "34141705065",
    // "34141703875",
    // "35072819832",
    // "35072820123",
    // "35072820436",
    // "35072820064",
    // "35072820414",
    // "35072815724" 
    po((new AramexTrackingServices())->TrackingDetails($tracking_id));

    // po($arrayResult);


  }

  public function AramexBooking()
  {

    $client = new Client();
    $headers = [
      'Content-Type' => 'application/json'
    ];

    $test = '';
    $body = '{
      "ClientInfo": {
      "UserName": "test.api@aramex.com",
      "Password": "Aramex@12345",
      "Version": "v1.0",
      "AccountNumber": "60531487",
      "AccountPin": "654654",
      "AccountEntity": "BOM",
      "AccountCountryCode": "IN",
      "Source": 24
    },

  "LabelInfo": {
    "ReportID": 9729,
    "ReportType": "URL"
  },

  "Shipments": [
    {
      "Reference1": "",
      "Reference2": "",
      "Reference3": "",
      "Shipper": {
        "Reference1": "",
        "Reference2": "",
        "AccountNumber": "60531487",
        "PartyAddress": {
          "Line1": "dwayne streey 123, jhsg",
          "Line2": "",
          "Line3": "",
          "City": "Mumbai",
          "StateOrProvinceCode": "",
          "PostCode": "400093",
          "CountryCode": "IN",
          "Longitude": 0,
          "Latitude": 0,
          "BuildingNumber": null,
          "BuildingName": null,
          "Floor": null,
          "Apartment": null,
          "POBox": null,
          "Description": null
        },
        "Contact": {
          "Department": "",
          "PersonName": "Dosan",
          "Title": "",
          "CompanyName": "jha pvt",
          "PhoneNumber1": "25655666",
          "PhoneNumber1Ext": "",
          "PhoneNumber2": "",
          "PhoneNumber2Ext": "",
          "FaxNumber": "",
          "CellPhone": "25655666",
          "EmailAddress": "dosan@gmail.com",
          "Type": ""
        }
      },
      "Consignee": {
        "Reference1": "",
        "Reference2": "",
        "AccountNumber": "",
        "PartyAddress": {
          "Line1": "1, bhat ji ki badi",
          "Line2": "",
          "Line3": "",
          "City": "Dubai",
          "StateOrProvinceCode": "",
          "PostCode": "",
          "CountryCode": "AE",
          "Longitude": 0,
          "Latitude": 0,
          "BuildingNumber": "",
          "BuildingName": "",
          "Floor": "",
          "Apartment": "",
          "POBox": null,
          "Description": ""
        },
        "Contact": {
          "Department": "",
          "PersonName": "Viki",
          "Title": "",
          "CompanyName": "hgh pvt ltd",
          "PhoneNumber1": "8454097313",
          "PhoneNumber1Ext": "",
          "PhoneNumber2": "",
          "PhoneNumber2Ext": "",
          "FaxNumber": "",
          "CellPhone": "8454097313",
          "EmailAddress": "vi@gmail.com",
          "Type": ""
        }
      },
      "ThirdParty": {
        "Reference1": "",
        "Reference2": "",
        "AccountNumber": "",
        "PartyAddress": {
          "Line1": "",
          "Line2": "",
          "Line3": "",
          "City": "",
          "StateOrProvinceCode": "",
          "PostCode": "",
          "CountryCode": "",
          "Longitude": 0,
          "Latitude": 0,
          "BuildingNumber": null,
          "BuildingName": null,
          "Floor": null,
          "Apartment": null,
          "POBox": null,
          "Description": null
        },
        "Contact": {
          "Department": "",
          "PersonName": "",
          "Title": "",
          "CompanyName": "",
          "PhoneNumber1": "",
          "PhoneNumber1Ext": "",
          "PhoneNumber2": "",
          "PhoneNumber2Ext": "",
          "FaxNumber": "",
          "CellPhone": "",
          "EmailAddress": "",
          "Type": ""
        }
      },
      "ShippingDateTime": "1671021297590.0",
      "DueDate": "1671021297590.0",
      "Comments": "",
      "PickupLocation": "",
      "OperationsInstructions": "",
      "AccountingInstrcutions": "",
      "Details": {
        "Dimensions": null,
        "ActualWeight": {
          "Unit": "KG",
          "Value": 2
        },
        "ChargeableWeight": null,
        "DescriptionOfGoods": "Books",
        "GoodsOriginCountry": "IN",
        "NumberOfPieces": 1,
        "ProductGroup": "EXP",
        "ProductType": "PPX",
        "PaymentType": "P",
        "PaymentOptions": "",
        "CustomsValueAmount": {
          "CurrencyCode": "USD",
          "Value": 200
        },
        "CashOnDeliveryAmount": null,
        "InsuranceAmount": null,
        "CashAdditionalAmount": null,
        "CashAdditionalAmountDescription": "",
        "CollectAmount": null,
        "Services": "",
        "Items": [
          {
            "PackageType": "Box",
            "Quantity": "1",
            "Weight": null,
            "CustomsValue": {
              "CurrencyCode": "USD",
              "Value": 10
            },
            "Comments": "Ravishing Gold Facial Kit Long Lasting Shining Appearance For All Skin Type 125g",
            "GoodsDescription": "new Gold Facial Kit Long  Shining Appearance",
            "Reference": "",
            "CommodityCode": "98765432"
          }
        ],
        "AdditionalProperties": [
          {
            "CategoryName": "CustomsClearance",
            "Name": "ShipperTaxIdVATEINNumber",
            "Value": "123456789101"
          },
          {
            "CategoryName": "CustomsClearance",
            "Name": "ConsigneeTaxIdVATEINNumber",
            "Value": "987654321012"
          },
          {
            "CategoryName": "CustomsClearance",
            "Name": "TaxPaid",
            "Value": "1"
          },
          {
            "CategoryName": "CustomsClearance",
            "Name": "InvoiceDate",
            "Value": "12/09/2022"
          },
          {
            "CategoryName": "CustomsClearance",
            "Name": "InvoiceNumber",
            "Value": "Inv123456"
          },
          {
            "CategoryName": "CustomsClearance",
            "Name": "TaxAmount",
            "Value": "120.52"
          },
          {
            "CategoryName": "CustomsClearance",
            "Name": "IOSS",
            "Value": "1098494352"
          },
          {
            "CategoryName": "CustomsClearance",
            "Name": "ExporterType",
            "Value": "UT"
          }
        ]
      },
      "Attachments": [],
      "ForeignHAWB": "",
      "TransportType ": 0,
      "PickupGUID": "",
      "Number": null,
      "ScheduledDelivery": null
    }
  ],
  "Transaction": {
    "Reference1": "",
    "Reference2": "",
    "Reference3": "",
    "Reference4": "",
    "Reference5": ""
  }
}';

    $request = new Http_request('POST', 'https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json/CreateShipments', $headers, $body);
    $res = $client->sendAsync($request)->wait();
    echo $res->getBody();
  }


  public function zohoWebhookResponse(Request $request)
  {
    Log::info($request->all());
  }
}
