<?php

namespace App\Http\Controllers;

use App\Models\Admin\Ratemaster;
use Exception;
use RedBeanPHP\R;
use App\Models\BOE;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Aws_credential;
use App\Models\Catalog\Asin_master;
use App\Models\Catalog\pricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Mws_region;
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
use Illuminate\Cache\RateLimiting\Limit;
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
    $apiInstance = new CatalogItemsV0Api($config);
    echo "<pre>";

    try {
      $result = $apiInstance->getCatalogItem($marketplace, $asin);
      $result = json_decode(json_encode($result));
      po($result);
    } catch (Exception $e) {
      echo 'Exception when calling CatalogApi->getCatalogItem: ', $e->getMessage(), PHP_EOL;
    }

    echo 'Product Pricing Api / getCompetitivePricing';
    echo "<hr>";

    exit;
    $apiInstance = new ProductPricingApi($config);
    try {
      $result = $apiInstance->getCompetitivePricing($marketplace, $item_type, $asins);
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

  public function getOrder($order_id, $seller_id, $country_code)
  {
    $token = NULL;
    $config = $this->config($seller_id, $country_code, $token);
    $marketplace_ids = $this->marketplace_id($country_code);
    $marketplace_ids = [$marketplace_ids];

    $apiInstance = new OrdersApi($config);
    $startTime = Carbon::now()->subDays(10)->toISOString();
    $createdAfter = $startTime;
    $max_results_per_page = 100;

    $next_token = NULL;
    $amazon_order_ids = [$order_id];
    // try {
    // $result_order_address = $apiInstance->getOrderAddress($order_id);
    // po($result_order_address);
    // exit;

    $order = $apiInstance->getOrders($marketplace_ids, $createdAfter, $created_before = null, $last_updated_after = null, $last_updated_before = null, $order_statuses = null, $fulfillment_channels = null, $payment_methods = null, $buyer_email = null, $seller_order_id = null, $max_results_per_page, $easy_ship_shipment_statuses = null, $next_token, $amazon_order_ids, $actual_fulfillment_supply_source_id = null, $is_ispu = null, $store_chain_store_id = null, $data_elements = null)->getPayload();

    po($order);
    exit;
    $data_element = array('buyerInfo');
    $next_token = NULL;

    echo '<hr>';
    echo 'Order item details';
    $result_orderItems = $apiInstance->getOrderItems($order_id, $next_token, $data_element);

    po($result_orderItems);

    echo '<hr>';
    echo 'Order Address';
    $result_order_address = $apiInstance->getOrderAddress($order_id);

    po($result_order_address);
  }

  public function SmsaTracking($awb_no)
  {
    $details = SmsaTrackingResponse($awb_no);
    po($details);
    exit;
  }

  public function BombinoTracking($awb_no)
  {
    $response = BombinoTrackingResponse($awb_no);
    po($response);
    exit;
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
      Asin_master::select('asin_masters.asin', "$catalog_table.package_dimensions")
        ->where('asin_masters.source', $country_code)
        ->join($catalog_table, 'asin_masters.asin', '=', "$catalog_table.asin")
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
    $rate_array = $this->rate_master_in_sa;
    $int_shipping_base_charge = '';
    foreach ($rate_array as $key => $value) {

      if ($key >= $weight) {
        $int_shipping_base_charge = $value['lmd_cost'];
        break;
      }
    }

    $duty_rate = 7 / 100;
    $nitshopp = 12.0 / 100;
    $packaging = 100.00;
    $amazon_commission = 15.0 / 100;
    $ex_rate = 0.051;

    $duty_cost = ($bb_price + $int_shipping_base_charge) * $duty_rate;

    $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
      (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $nitshopp);

    $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
      ($amazon_commission * $price_befor_amazon_fees * 0.14);

    $uae_sa = $mbm_usd_sp * $ex_rate;

    return round($uae_sa, 2);
  }

  public function INDToSG($weight, $bb_price)
  {
    //India to Singapore
    $rate_array = $this->rate_master_in_sg;
    $int_shipping_base_charge = '';
    foreach ($rate_array as $key => $value) {

      if ($key >= $weight) {
        $int_shipping_base_charge = $value['lmd_cost'];
        break;
      }
    }

    $duty_rate = 7 / 100;
    $nitshopp = 15.0 / 100;
    $packaging = 120.00;
    $amazon_commission = 15.0 / 100;
    $ex_rate = 0.019;

    $duty_cost = ($bb_price + $int_shipping_base_charge) * $duty_rate;

    $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
      (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $nitshopp);

    $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
      ($amazon_commission * $price_befor_amazon_fees * 0.14);

    $uae_sg = $mbm_usd_sp * $ex_rate;

    return round($uae_sg, 2);
  }

  public function INDToUAE($weight, $bb_price)
  {
    $rate_array = $this->rate_master_in_ae;
    $int_shipping_base_charge = '';

    foreach ($rate_array as $key => $value) {

      if ($key >= $weight) {
        $int_shipping_base_charge = $value['lmd_cost'];
        break;
      }
    }
    $duty_rate = 7 / 100;
    $nitshopp = 12.0 / 100;
    $packaging = 180.00;
    $amazon_commission = 15.0 / 100;
    $ex_rate = 0.051;

    $duty_cost = ($bb_price + $int_shipping_base_charge) * $duty_rate;

    $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
      (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $nitshopp);

    $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
      ($amazon_commission * $price_befor_amazon_fees * 0.14);

    $uae_sp = $mbm_usd_sp * $ex_rate;

    return round($uae_sp, 2);
  }

  public function USAToIND($weight, $bb_price)
  {
    if ($weight > 0.9) {
      $int_shipping_base_charge = (6 + ($weight - 1) * 6);
    } else {
      $int_shipping_base_charge = 6;
    }

    $duty_rate = 32.00 / 100;
    $seller_commission = 10 / 100;
    $packaging = 2;
    $amazon_commission = 22.00 / 100;

    $ex_rate = 82;
    $duty_cost = round(($duty_rate * ($bb_price + $int_shipping_base_charge)), 2);

    $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
      (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $seller_commission);

    $usd_sp = round($price_befor_amazon_fees * (1 + $amazon_commission) +
      ($amazon_commission * $price_befor_amazon_fees * 0.12), 2);

    $india_sp = $usd_sp * $ex_rate;
    return $india_sp;
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
    $duty_rate = 5 / 100;
    $seller_commission = 10 / 100;
    $packaging = 4;
    $amazon_commission = 15.00 / 100;
    $int_shipping_base_charge = $weight * 4.5;
    $ex_rate = 3.7;
    $duty_cost = round(($duty_rate * ($bb_price + $int_shipping_base_charge)), 2);

    $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
      (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $seller_commission);

    $usd_sp = round($price_befor_amazon_fees * (1 + $amazon_commission) +
      ($amazon_commission * $price_befor_amazon_fees * 0.12), 2);

    $IED_sp = $usd_sp * $ex_rate;
    return round($IED_sp, 2);
  }

  public function USATOSG($weight, $bb_price)
  {
    if ($weight > 0.9) {
      $int_shipping_base_charge = (8 + ($weight - 1) * 4.5);
    } else {
      $int_shipping_base_charge = 8;
    }

    // return $int_shipping_base_charge;
    $duty_rate = 4.00 / 100;
    $seller_commission = 10 / 100;
    $packaging = 3;
    $MBM = 10.0 / 100;
    $amazon_commission = 12.00 / 100;

    $ex_rate = 1.37;
    $duty_cost = $duty_rate * $bb_price;

    $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
      (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $MBM);

    $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
      ($amazon_commission * $price_befor_amazon_fees * 0.12);

    $sg_sp = $mbm_usd_sp * $ex_rate;

    return round($sg_sp, 2);
    //
  }
}
