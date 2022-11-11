<?php

namespace App\Services\Zoho;

use in;
use DateTime;
use Carbon\Carbon;
use App\Models\order\Order;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_us;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\order\OrderItemDetails;
use App\Models\order\OrderUpdateDetail;

class ZohoOrder
{
    public $store_lists = [
        "Gotech-Saudi" => [
            "SA" => "Nit Shopp KSA",
            "sku" => "NT_",
            "source" => "India",
            "desination" => "KSA"
        ],
        "Gotech UAE" => [
            "AE" => "Nit Shopp UAE",
            "sku" => "NT_",
            "source" => "India",
            "desination" => "UAE"
        ],
        "Gotech USA" => [
            "US" => "Nit Shopp USA",
            "sku" => "NT_",
            "source" => "India",
            "desination" => "USA"
        ],
        "Amazon.in-Pram" => [
            "IN" => "Infinitikart India",
            "sku" => "PR_",
            "source" => "USA",
            "desination" => "India"
        ],
        "Amazon.sa-Infinitikart" => [
            "SA" => "Infinitikart KSA",
            "sku" => "PR_",
            "source" => "India",
            "desination" => "KSA"
        ],
        "PRAM UAE" => [
            "AE" => "Infinitikart UAE",
            "sku" => "IFWH_",
            "source" => "India",
            "desination" => "UAE"
        ],
        "Amazon.in-MBM" => [
            "IN" => "MBM India",
            "sku" => "MBM_",
            "source" => "USA",
            "desination" => "India"
        ],
        "MBM-SAUDI" => [
            "SA" => "MBM KSA",
            "sku" => "MBM_",
            "source" => "USA",
            "desination" => "KSA"
        ],
        "Amazon.ae-MBM" => [
            "AE" => "MBM UAE",
            "sku" => "MBM_",
            "source" => "USA",
            "desination" => "UAE"
        ],
        "Amazon.ae-New Media" => [
            "AE" => "New Media Store",
            "sku" => "NM_",
            "source" => "India",
            "desination" => "UAE"
        ],
        "Amazon.in-Nitrous" => [
            "IN" => "Nitrous Stores India",
            "sku" => "NS_",
            "source" => "USA",
            "desination" => "India"
        ],
        "Amazon.ae-Mahzuz" => [
            "AE" => "Mahzuz Stores UAE",
            "sku" => "MZ_",
            "source" => "USA",
            "desination" => "UAE"
        ],
        "CKSHOP-Amazon.in" => [
            "IN" => "STS Shop India",
            "sku" => "CK_",
            "source" => "USA",
            "desination" => "India"
        ],
        "Amazon.in-Gotech" => [
            "IN" => "M.A.Y. Store India (Nit)",
            "sku" => "NT_",
            "source" => "USA",
            "desination" => "India"
        ],

        /*
        "Amazon.ae-Nitrous" => [
            "IN" => "WIP",
            "sku" => "NS_",
            "source" => "USA",
            "desination" => "UAE"
        ],
        "Amazon.sg-Gotech" => [
            "IN" => "WIP",
            "sku" => "NT_",
            "source" => "India",
            "desination" => "SG"
        ],
        "Amazon.sg-Nitrous" => [
            "IN" => "WIP",
            "sku" => "NS_",
            "source" => "USA",
            "desination" => "SG"
        ]
        */
    ];

    public function index($amazon_order_id = null)
    {

        // if (!$amazon_order_id) {
        //     $orderItems = OrderUpdateDetail::whereNull('zoho_id')->limit(1)->first();
        //     $amazon_order_id = $orderItems->amazon_order_id;
        // } else {
        $orderItems = OrderUpdateDetail::where('amazon_order_id', $amazon_order_id)->whereNull('zoho_id')->limit(1)->first();
        $amazon_order_id = $orderItems->amazon_order_id;
        // }

        if (!$amazon_order_id) {
            Log::channel('slack')->error('Amazon Order id not passed');
            return true;
        }

        $order_table_name = 'orders';
        $order_item_table_name = 'orderitemdetails';

        $order_details = [
            "$order_item_table_name.seller_identifier",
            "$order_item_table_name.asin",
            "$order_item_table_name.seller_sku",
            "$order_item_table_name.title",
            "$order_item_table_name.order_item_identifier",
            "$order_item_table_name.quantity_ordered",
            "$order_item_table_name.item_price",
            "$order_table_name.fulfillment_channel",
            "$order_table_name.our_seller_identifier",
            "$order_table_name.amazon_order_identifier",
            "$order_table_name.purchase_date",
            "$order_item_table_name.shipping_address",
            "$order_table_name.earliest_delivery_date",
            "$order_table_name.buyer_info",
            "$order_table_name.order_total",
            "$order_table_name.latest_delivery_date",
            "$order_table_name.is_business_order",
        ];

        $order_item_details = OrderItemDetails::select($order_details)
            ->join('orders', 'orderitemdetails.amazon_order_identifier', '=', 'orders.amazon_order_identifier')
            ->where('orderitemdetails.amazon_order_identifier', $amazon_order_id)
            ->with(['store_details.mws_region'])
            ->limit(1)
            ->first();

        if ($order_item_details) {

            $auth_token = $this->getAccessToken();
            $prod_array = $this->zohoOrderFormating($order_item_details);

            $zoho_api_save = $this->insertOrderItemsToZoho($prod_array, $auth_token);
            $zoho_response = json_decode($zoho_api_save, true);
            Log::channel('slack')->error("Zoho Response : " . $zoho_api_save);
            if (array_key_exists('data', $zoho_response) && array_key_exists(0, $zoho_response['data']) && array_key_exists('code', $zoho_response['data'][0])) {

                $zoho_save_id = $zoho_response['data'][0]['details']['id'];

                $order_zoho = [
                    "amazon_order_id" => $amazon_order_id,
                    "order_item_id" => $prod_array['Payment_Reference_Number'],
                    "zoho_id" => $zoho_save_id
                ];

                $order_response = OrderUpdateDetail::upsert($order_zoho, ["amazon_order_id", "order_item_id"], ["zoho_id"]);

                if ($order_response) {
                    return Log::channel('slack')->error('Success');
                } else {
                    return Log::channel('slack')->error('Error: ' . json_encode($order_response));
                }
            } else {

                Log::channel('slack')->error("Zoho Response : " . json_encode($zoho_response));
            }
        }


        return true;
    }

    public function getAccessToken()
    {
        $zohoURL = "https://accounts.zoho.com/oauth/v2/token";

        $client_id = config('app.zoho_client_id');
        $client_secret = config('app.zoho_secret');
        $refres_token = config('app.zoho_refresh_token');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $zohoURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER =>  false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refres_token,
                'grant_type' => 'refresh_token'
            ),
        ));

        $response = curl_exec($curl);
        po($response);

        curl_close($curl);
        $response = json_decode($response, true);

        return $response['access_token'] ?? null;
    }

    public function insertOrderItemsToZoho($prod_array, $auth_token)
    {
        $curl_pointer = curl_init();

        $curl_options = array();
        $url = "https://www.zohoapis.com/crm/v2/Leads";

        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_RETURNTRANSFER] = true;
        $curl_options[CURLOPT_HEADER] = 1;
        $curl_options[CURLOPT_CUSTOMREQUEST] = "POST";
        $requestBody = array();
        $recordArray = array();
        $recordObject = array();

        $recordArray[] = $prod_array;
        $requestBody["data"] = $recordArray;
        $curl_options[CURLOPT_POSTFIELDS] = json_encode($requestBody);
        $headersArray = array();

        $headersArray[] = "Authorization" . ":" . "Zoho-oauthtoken $auth_token";

        $curl_options[CURLOPT_HTTPHEADER] = $headersArray;

        curl_setopt_array($curl_pointer, $curl_options);

        $result = curl_exec($curl_pointer);

        return $result;
    }

    public function zohoOrderFormating($value)
    {
        $DOLLAR_EXCHANGE_RATE = 82;
        $AED_EXCHANGE_RATE = 3.8;

        $buyerDtls = (object)$value->shipping_address;
        $store_name = $this->get_store_name($value->store_details);
        $country_code = $this->get_country_code($value->store_details);
        $buyerEmail = json_decode($value->buyer_info);
        $order_total = json_decode($value->order_total);
        $item_price = json_decode($value->item_price);

        $prod_array = [];

        ############################
        ### Customer Information ###
        ############################

        $prod_array["Order_Creation_Date"]  = Carbon::parse($value->purchase_date)->format(DateTime::ATOM);

        $prod_array['Alternate_Order_No'] = $value->amazon_order_identifier;
        $prod_array['Follow_up_Status'] = 'Open';

        $prod_array["Last_Name"]   = "Testing $buyerDtls->Name";
        $prod_array["Lead_Source"] = $this->lead_source($store_name, $country_code);
        $prod_array['Lead_Status'] = $this->lead_status($store_name, $country_code);

        $address = $buyerDtls->AddressLine1 . '<br> ' . $buyerDtls->AddressLine2;
        $address = str_replace("&", " and ", $address);

        $prod_array["Mobile"]      = substr((int) filter_var($buyerDtls->Phone, FILTER_SANITIZE_NUMBER_INT), -10);
        $prod_array["Address"]     = $address;
        $prod_array["City"]        = $buyerDtls->City;
        $prod_array['State']          = $this->get_state_pincode($country_code, $buyerDtls);
        $prod_array['Zip_Code']       = $this->get_state_pincode($country_code, $buyerDtls, 'pincode');

        $prod_array["Email"]              = ((isset($buyerEmail->BuyerEmail)) ? $buyerEmail->BuyerEmail : '');
        $prod_array["Customer_Type"]      = ($value->is_business_order == 'true') ? 'B2B' : 'B2C';
        $prod_array["Fulfilment_Channel"] = $this->fulfillment_channel($value->fulfillment_channel);

        ############################
        ### Inventory Management ###
        ############################

        $catalog_details = $this->get_catalog($value->asin, $country_code, $store_name);

        $prod_array["Designation"]              = preg_replace("/[^a-zA-Z0-9_ -\/]+/", "", substr($value->title, 0, 100));
        $prod_array["Product_Code"]       = $value->seller_sku;
        $prod_array["Product_Cost"]       = $item_price->Amount;
        $prod_array["Procurement_URL"]    = $this->get_procurement_link($country_code, $value->asin);
        $prod_array["Nature"]             = "Import";
        $prod_array["Product_Category"]   = $catalog_details['category']; //$value->product_category;
        $prod_array["Quantity"]           = "$value->quantity_ordered";

        ###############################
        ### Procurement Information ###
        ###############################

        $prod_array["Product_Link"]              = $this->get_product_link($country_code, $value->asin);
        $prod_array["US_EDD"]                    = Carbon::parse($value->latest_delivery_date)->format('Y-m-d');

        $prod_array["ASIN"]                      = $value->asin;
        $prod_array["SKU"]                       = $value->seller_sku;
        $prod_array["Product_Cost"]              = $catalog_details['price'];

        $prod_array["Weight_in_LBS"]             = (string)$catalog_details['weight'];
        $prod_array["Payment_Reference_Number"]  = $value->order_item_identifier;
        $prod_array["Exchange"]                  = $DOLLAR_EXCHANGE_RATE;

        return $prod_array;
    }

    public function lead_source($store_name, $country_code)
    {
        if (is_null($store_name)) {
            return "Amazon.in";
        }

        $lead_name = '';

        foreach ($this->store_lists as $lead_key_name => $store_list) {

            if (isset($store_list[$country_code]) && $store_list[$country_code] == $store_name) {
                $lead_name = $lead_key_name;
                break;
            }
        }

        return $lead_name;
    }

    public function lead_status($store_name, $country_code)
    {

        if (($store_name == 'Nitrous Stores' && $country_code == 'IN') || ($store_name == 'MBM India Stores' && $country_code == 'IN')) {

            return 'B2C Order Confirmed KYC Pending';
        }

        return 'Order Confirmed Purchase Pending';
    }

    public function fulfillment_channel($fulfillment_channel)
    {

        if ($fulfillment_channel == 'AFN') {
            return 'Amazon Fulfilment';
        }

        return 'Merchant Fulfilment';
    }

    public function get_store_name($value)
    {
        if (!isset($value)) {
            return '';
        }

        return $value->store_name;
    }

    public function get_country_code($mws_region_object)
    {

        if (!isset($mws_region_object)) {
            return '';
        }

        if (!isset($mws_region_object->mws_region)) {
            return '';
        }

        return $mws_region_object->mws_region->region_code;
    }

    public function get_procurement_link($country_code, $asin)
    {
        if ($country_code != 'AE') {
            return 'http://www.amazon.in/gp/product/' . $asin;
        }

        return 'http://www.amazon.com/gp/product/' . $asin;
    }

    public function get_product_link($country_code, $asin)
    {
        if ($country_code != 'AE') {

            return 'http://www.amazon.ae/gp/product/' . $asin;
        }

        return 'http://www.amazon.in/gp/product/' . $asin;
    }

    public function get_state_pincode($country_code, $buyerDtls, $return = 'state')
    {

        if ($country_code == 'AE') {

            if ($return == "state") {
                return $buyerDtls->City;
            }

            return '00000';
        }

        if ($return == "state") {
            return $buyerDtls->StateOrRegion;
        }

        return $buyerDtls->PostalCode ?? '00000';
    }

    public function get_catalog($asin, $country_code, $store_name)
    {
        $result = null;
        $price = 0;
        $weight = 0;
        $category = '';

        foreach ($this->store_lists as $store_list) {

            if (isset($store_list[$country_code]) && $store_list[$country_code] == $store_name) {

                if ($store_list['source'] == "USA") {

                    $result = Catalog_us::where('asin', $asin)->limit(1)->first();
                    $result_price = PricingUs::where('asin', $asin)->limit(1)->first();
                    $price = $result_price->us_price;
                } else {
                    $result = Catalog_in::where('asin', $asin)->limit(1)->first();
                    $result_price = PricingIn::where('asin', $asin)->limit(1)->first();
                    $price = $result_price->in_price;
                }

                if (isset($result) && isset($result->dimensions[0]['package']['weight']) && $result->dimensions[0]['package']['weight']['unit'] == 'pounds') {

                    $weight = ceil($result->dimensions[0]['package']['weight']['value']);
                }

                if (isset($result) && isset($result->browse_classification['displayName'])) {

                    $category = $result->browse_classification['displayName'];
                }

                break;
            }
        }

        return ['price' => $price, 'weight' => $weight, 'category' => $category];
    }
}
