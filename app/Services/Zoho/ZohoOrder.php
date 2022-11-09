<?php

namespace App\Services\Zoho;

use Carbon\Carbon;
use App\Models\order\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\order\OrderItemDetails;
use App\Models\order\OrderUpdateDetail;

class ZohoOrder
{

    public function index()
    {
        $orderItems = OrderUpdateDetail::whereNull('courier_name')->whereNull('courier_awb')->limit(1)->first();

        if ($orderItems) {

            $order_table_name = 'orders';
            $order_item_table_name = 'orderitemdetails';

            $order_details = [
                "$order_item_table_name.seller_identifier",
                "$order_item_table_name.asin",
                "$order_item_table_name.seller_sku",
                "$order_item_table_name.title",
                "$order_table_name.fulfillment_channel",
                "$order_table_name.our_seller_identifier",
                "$order_table_name.amazon_order_identifier",
                "$order_table_name.purchase_date",
                "$order_item_table_name.shipping_address",
                "$order_table_name.earliest_delivery_date",
                "$order_table_name.buyer_info"
            ];

            $order_item_details = OrderItemDetails::select($order_details)
                ->join('orders', 'orderitemdetails.amazon_order_identifier', '=', 'orders.amazon_order_identifier')
                // ->join('orders', 'orderitemdetails.amazon_order_identifier', '=', 'orders.amazon_order_identifier')
                ->where('orderitemdetails.amazon_order_identifier', $orderItems->amazon_order_id)
                ->with(['store_details.mws_region'])
                ->limit(1)
                ->first();

            if ($order_item_details) {

                // dd($order_item_details);
                dd($this->zohoOrderFormating($order_item_details));
            }
        }

        return "No Data";
    }

    public function getAccessToken()
    {
        $zohoURL = "https://accounts.zoho.in/oauth/v2/token";

        $client_id = config('app.zoho_client_id');
        $client_secret = config('app.zoho_secret');
        $refres_token = config('app.zoho_refresh_token');
        // dd($client_id);
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
        curl_close($curl);
        $response = json_decode($response, true);

        // dd($response);
        return $response['access_token'];
    }

    public function insertOrderItemsToZoho($prod_array, $auth_token)
    {
        $curl_pointer = curl_init();

        $curl_options = array();
        $url = "https://www.zohoapis.in/crm/v2/Leads";

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


    public function getOrderDetails()
    {
        $orderItems = DB::connection('order')
            ->select("SELECT *, oid.shipping_address 
        FROM 
            orders AS os
        INNER JOIN orderitemdetails AS oid

        ON
            os.amazon_order_identifier = oid.amazon_order_identifier
        INNER JOIN ord_order_seller_credentials AS oosc
        ON
            oosc.seller_id = os.our_seller_identifier
        LIMIT 1
        ");

        if (count($orderItems) > 0) {
            foreach ($orderItems as $value) {

                // dd('test');
                $access_token = $this->getAccessToken();
                $order_item_zoho = $this->zohoOrderFormating($value);

                $zoho_response = $this->insertOrderItemsToZoho($order_item_zoho, $access_token);


                $content = preg_split('/[\r\n]/', $zoho_response, -1, PREG_SPLIT_NO_EMPTY);
                $array = (($content[count($content) - 1]));

                $result = (json_decode($array));
                $response['id'] = ($result->data[0]->details->id);
                $response['status'] = ($result->data[0]->status);
                $leadId = $response['id'];
                po($response);
                $this->zohoOrderDetails($leadId);
            }
        }
    }

    public function zohoOrderFormating($value)
    {
        $order_data = Carbon::parse($value->purchase_date)->format('Y-m-d H:i:s');

        $prod_array = [];
        $prod_array['Alternate_Order_No'] = $value->amazon_order_identifier;
        $prod_array['Follow_up_Status'] = 'Open';

        $store_name = '';
        $country_code = '';

        if (isset($value->store_details)) {
            $store_name = $value->store_details->store_name;

            if (isset($value->store_details->mws_region)) {
                $country_code = $value->store_details->mws_region->region_code;
            }
        }

        if (($store_name == 'Nitrous Stores' && $country_code == 'IN') || ($store_name == 'MBM India Stores' && $country_code == 'IN')) {

            $prod_array['Lead_Status'] = 'B2C Order Confirmed KYC Pending';
        } else {

            $prod_array["Lead_Status"] = 'Order Confirmed Purchase Pending';
        }



        $prod_array["Lead_Source"] = "Amazon.in";

        $buyerDtls = (object)$value->shipping_address;
        $address = $buyerDtls->AddressLine1 . '<br> ' . $buyerDtls->AddressLine2;
        $address = str_replace("&", " and ", $address);

        $prod_array["Last_Name"]                 = $buyerDtls->Name;
        $prod_array["Mobile"]                    = substr((int) filter_var($buyerDtls->Phone, FILTER_SANITIZE_NUMBER_INT), -10);
        $prod_array["Address"]                   = $address;
        $prod_array["City"]                      = $buyerDtls->City;
        $prod_array["State"]                     = '';
        $prod_array["Zip_Code"]                  = '';

        $buyerEmail = json_decode($value->buyer_info);
        $prod_array["Email"]                     = ((isset($buyerEmail->BuyerEmail)) ? $buyerEmail->BuyerEmail : '');
        $prod_array["Customer_Type1"]            = '';


        po($prod_array);

        exit;

        $order_total = json_decode($value->order_total);
        $prod_array["Amount_Paid_by_Customer"]   = (int)$order_total->Amount;
        $prod_array["Designation"]               = preg_replace("/[^a-zA-Z0-9_ -\/]+/", "", substr($value->title, 0, 100));
        $prod_array["Order_Creation_Date"]       = $order_data;
        $prod_array["Product_Code"]              = $value->seller_sku;


        if ($value->country_code != 'AE') {
            $prod_array["Procurement_URL"] = 'http://www.amazon.in/gp/product/' . $value->asin;
            $prod_array["Product_Link"] = 'http://www.amazon.ae/gp/product/' . $value->asin;
        } else {
            $prod_array["Procurement_URL"] = 'http://www.amazon.com/gp/product/' . $value->asin;
            $prod_array["Product_Link"] = 'http://www.amazon.in/gp/product/' . $value->asin;
        }

        $prod_array["US_EDD"]                    = Carbon::parse($value->latest_delivery_date)->format('Y-m-d');

        $item_price = json_decode($value->item_price);
        $prod_array["Product_Cost"]              = $item_price->Amount;
        $prod_array["Product_Category"]          = ''; //$value->product_category;
        $prod_array["Item_Type_Category"]        = ''; //$value->item_type_category;

        $product_info = json_decode($value->product_info);
        $prod_array["Quantity"]                  = $product_info->NumberOfItems;
        $prod_array["ASIN"]                      = $value->asin;
        $prod_array["SKU"]                       = $value->seller_sku;
        $prod_array["H_Code"]                    = ''; //$value->hs_code;
        $prod_array["GST"]                       = ''; //$value->gst;

        if ($value->store_name == 'Mahzuz Stores (Seller)') {
            $prod_array["Lead_Source"] = 'Amazon.ae-Mahzuz';
        } else {
            $prod_array["Lead_Source"] = $value->store_name;
        }

        $prod_array["Fulfilment_Channel"]        = $value->fulfillment_channel;
        // $prod_array["Weight_in_LBS"]             = (string)ceil($value->weight_in_lbs);
        $prod_array["Payment_Reference_Number1"] = $value->order_item_identifier;
        $prod_array["Exchange"]                  = 80;
        $prod_array["Nature"]                    = "Import";

        return $prod_array;
    }

    public function zohoOrderFormating_old($value)
    {
        $order_data = Carbon::parse($value->purchase_date)->format('Y-m-d H:i:s');

        $prod_array = [];
        $prod_array['Alternate_Order_No'] = $value->amazon_order_identifier;
        $prod_array['Follow_up_Status'] = 'Open';

        if (($value->store_name == 'Nitrous Stores' && $value->country_code == 'IN') || ($value->store_name == 'MBM India Stores' && $value->country_code == 'IN')) {

            $prod_array['Lead_Status'] = 'B2C Order Confirmed KYC Pending';
        } else {

            $prod_array["Lead_Status"] = 'Order Confirmed Purchase Pending';
        }
        $prod_array["Lead_Source"] = "Amazon.in";

        $buyerDtls = json_decode($value->shipping_address);
        $address = $buyerDtls->AddressLine1 . '<br> ' . $buyerDtls->AddressLine2;
        $address = str_replace("&", " and ", $address);

        $prod_array["Last_Name"]                 = $buyerDtls->Name;
        $prod_array["Mobile"]                    = substr((int) filter_var($buyerDtls->Phone, FILTER_SANITIZE_NUMBER_INT), -10);
        $prod_array["Address"]                   = $address;
        $prod_array["City"]                      = $buyerDtls->City;
        $prod_array["State"]                     = '';
        $prod_array["Zip_Code"]                  = '';

        $buyerEmail = json_decode($value->buyer_info);
        $prod_array["Email"]                     = ((isset($buyerEmail->BuyerEmail)) ? $buyerEmail->BuyerEmail : '');
        $prod_array["Customer_Type1"]            = '';

        $order_total = json_decode($value->order_total);
        $prod_array["Amount_Paid_by_Customer"]   = (int)$order_total->Amount;
        $prod_array["Designation"]               = preg_replace("/[^a-zA-Z0-9_ -\/]+/", "", substr($value->title, 0, 100));
        $prod_array["Order_Creation_Date"]       = $order_data;
        $prod_array["Product_Code"]              = $value->seller_sku;


        if ($value->country_code != 'AE') {
            $prod_array["Procurement_URL"] = 'http://www.amazon.in/gp/product/' . $value->asin;
            $prod_array["Product_Link"] = 'http://www.amazon.ae/gp/product/' . $value->asin;
        } else {
            $prod_array["Procurement_URL"] = 'http://www.amazon.com/gp/product/' . $value->asin;
            $prod_array["Product_Link"] = 'http://www.amazon.in/gp/product/' . $value->asin;
        }

        $prod_array["US_EDD"]                    = Carbon::parse($value->latest_delivery_date)->format('Y-m-d');

        $item_price = json_decode($value->item_price);
        $prod_array["Product_Cost"]              = $item_price->Amount;
        $prod_array["Product_Category"]          = ''; //$value->product_category;
        $prod_array["Item_Type_Category"]        = ''; //$value->item_type_category;

        $product_info = json_decode($value->product_info);
        $prod_array["Quantity"]                  = $product_info->NumberOfItems;
        $prod_array["ASIN"]                      = $value->asin;
        $prod_array["SKU"]                       = $value->seller_sku;
        $prod_array["H_Code"]                    = ''; //$value->hs_code;
        $prod_array["GST"]                       = ''; //$value->gst;

        if ($value->store_name == 'Mahzuz Stores (Seller)') {
            $prod_array["Lead_Source"] = 'Amazon.ae-Mahzuz';
        } else {
            $prod_array["Lead_Source"] = $value->store_name;
        }

        $prod_array["Fulfilment_Channel"]        = $value->fulfillment_channel;
        // $prod_array["Weight_in_LBS"]             = (string)ceil($value->weight_in_lbs);
        $prod_array["Payment_Reference_Number1"] = $value->order_item_identifier;
        $prod_array["Exchange"]                  = 80;
        $prod_array["Nature"]                    = "Import";

        return $prod_array;
    }

    public function zohoOrderDetails($leadId)
    {

        $token = $this->getAccessToken();
        $headers = [
            'Authorization' => 'Zoho-oauthtoken ' . $token,
        ];
        $zohoURL = 'https://www.zohoapis.in/crm/v2/Leads/';

        $CompleteURI = $zohoURL . $leadId;
        $response = Http::withHeaders($headers)->get($CompleteURI);
        $response = json_decode($response);
        dd($response);
        exit;
    }
}
