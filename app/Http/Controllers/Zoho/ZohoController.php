<?php

namespace App\Http\Controllers\Zoho;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ZohoController extends Controller
{
    private function getAccessToken()
    {
        $zohoURL = "https://accounts.zoho.in/oauth/v2/token";

        $clientID = '1000.FY2B09NCY9PFBOT4FTRM0GEMXKCO2I';

        $clientSecret = 'd050ac81701d158c1903037082674034ace0d9538f';

        $grantType = 'refresh_token';
        $refreshToken = '1000.b3d61045a34455d7bff812b726e835ef.724fe9a752f343e4646436c974895534';

        $CompleteURI = $zohoURL . "?refresh_token=" . $refreshToken . "&client_id=" . $clientID . "&client_secret=" . $clientSecret . "&grant_type=" . $grantType;

        $response = Http::post($CompleteURI);

        $response = json_decode($response);
        // dd($response);
        return $response->access_token;
    }

    public function getOrderDetails(Request $request, $leadId)
    {
        $leadId = trim($leadId, 'zcrm_');

        $accessToken = $this->getAccessToken();
        $headers = [
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
        ];
        $zohoURL = 'https://www.zohoapis.com/crm/v2/Leads/';

        $CompleteURI = $zohoURL . $leadId;
        $response = Http::withHeaders($headers)->get($CompleteURI);
        $response = json_decode($response);
        $response = ($response->data[0]);
        // echo '<pre>';
        // print_r($response);
        // echo '</pre>';

        return response()->json($response);
    }

    public function insertZohoOrder($data, $accessToken)
    {

        $post = array("data" => $data);
        $post = json_encode($post);
        echo '<pre>';
        //print_r($post);
        print_r($post);
        echo '</pre>';

        $headers = [
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
            'Content-Type: text/json'
        ];
        $zohoURL = 'https://www.zohoapis.com/crm/v2/Leads';
        //$CompleteURI = $zohoURL;

        $response = Http::withHeaders($headers)->post($zohoURL, [$post]);

        $response = json_decode($response);
        return $response;
    }


    public function addOrderItemsToZoho()
    {
        //$accessToken = $this->getAccessToken();

        // $orderItems = DB::connection('order')->select("
        //     SELECT
        //     *,  oid.shipping_address
        //     FROM
        //         ord_zoho_models AS ozm
        //     INNER JOIN orderitemdetails AS oid
        //     ON
        //         oid.id = ozm.order_identifier
        //     INNER JOIN ord_order_seller_credentials AS oosc
        //     ON
        //         oosc.seller_id = ozm.seller_identifier
        //     INNER JOIN orders AS os
        //     ON
        //     os.amazon_order_identifier = oid.amazon_order_identifier
        //     WHERE
        //     ozm.zoho_id = ''
        // ");

        $orderItems = DB::connection('order')->select("
            SELECT *, oid.shipping_address 
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
            $count = 0;
            foreach ($orderItems as $val1) {

                $accessToken = $this->getAccessToken();

                $val1 = json_decode(json_encode($val1), true);
                $OrderItemtoZoho = $this->insertOrderItemtoZoho($val1);

                $responseZoho = $this->insertZohoOrder($OrderItemtoZoho, $accessToken);
                po($responseZoho);
                exit;
            }
        }
    }

    public function insertOrderItemtoZoho($orderitem)
    {
        $orderDate = Carbon::parse($orderitem["purchase_date"])->format('Y-m-d H:i:s');

        $prodarray = array();
        $prodarray["Alternate_Order_No"]        = $orderitem["amazon_order_identifier"];
        $prodarray["Follow_up_Status"]          = 'Open';
        if ($orderitem["store_name"] == 'nitrous' || $orderitem["store_name"] == 'in_mbm') {
            $prodarray["Lead_Status"] = 'B2C Order Confirmed KYC Pending';
        } else {
            $prodarray["Lead_Status"] = 'Order Confirmed Purchase Pending';
        }
        $prodarray["Lead_Source"]               = "Amazon.in";

        $buyerDtls = json_decode($orderitem["shipping_address"]);
        $address = $buyerDtls->AddressLine1 . '<br> ' . $buyerDtls->AddressLine2;
        $address = str_replace("&", " and ", $address);

        $prodarray["Last_Name"]                 = $buyerDtls->Name;
        $prodarray["Mobile"]                    = substr((int) filter_var($buyerDtls->Phone, FILTER_SANITIZE_NUMBER_INT), -10);
        $prodarray["Address"]                   = $address;
        $prodarray["City"]                      = $buyerDtls->City;
        $prodarray["State"]                     = '';
        $prodarray["Zip_Code"]                  = '';

        $buyerEmail = json_decode($orderitem["buyer_info"]);

        $prodarray["Email"]                     = ((isset($buyerEmail->BuyerEmail)) ? $buyerEmail->BuyerEmail : '');
        $prodarray["Email"]                     = '';
        $prodarray["Customer_Type1"]            = '';
        $order_total = json_decode($orderitem["order_total"]);

        $prodarray["Amount_Paid_by_Customer"]   = ((isset($order_total->Amount)) ? $order_total->Amount : '');
        $prodarray["Designation"]               = preg_replace("/[^a-zA-Z0-9_ -\/]+/", "", substr($orderitem["title"], 0, 100));
        $prodarray["Order_Creation_Date"]       = $orderDate;
        $prodarray["Product_Code"]              = $orderitem["seller_sku"];

        if ($orderitem["store_name"] != 'Amazon.in-Gotech') {
            $prodarray["Procurement_URL"] = 'http://www.amazon.in/gp/product/' . $orderitem['asin'];
            $prodarray["Product_Link"] = 'http://www.amazon.ae/gp/product/' . $orderitem['asin'];
        } else {
            $prodarray["Procurement_URL"] = 'http://www.amazon.com/gp/product/' . $orderitem['asin'];
            $prodarray["Product_Link"] = 'http://www.amazon.in/gp/product/' . $orderitem['asin'];
        }

        $prodarray["US_EDD"]                    = Carbon::parse($orderitem["latest_delivery_date"])->format('Y-m-d');

        $item_price = json_decode($orderitem["item_price"]);
        $prodarray["Product_Cost"]              = $item_price->Amount;
        $prodarray["Product_Category"]          = ''; //$orderitem["product_category"];
        $prodarray["Item_Type_Category"]        = ''; //$orderitem["item_type_category"];

        $product_info = json_decode($orderitem["product_info"]);
        $prodarray["Quantity"]                  = $product_info->NumberOfItems;
        $prodarray["ASIN"]                      = $orderitem["asin"];
        $prodarray["SKU"]                       = $orderitem["seller_sku"];
        $prodarray["H_Code"]                    = ''; //$orderitem["hs_code"];
        $prodarray["GST"]                       = ''; //$orderitem["gst"];

        if ($orderitem["store_name"] == 'mahzuz_uae') {
            $prodarray["Lead_Source"] = 'Amazon.ae-Mahzuz';
        } else {
            $prodarray["Lead_Source"] = $orderitem["store_name"];
        }

        $prodarray["Fulfilment_Channel"]        = $orderitem["fulfillment_channel"];
        // $prodarray["Weight_in_LBS"]             = (string)ceil($orderitem["weight_in_lbs"]);
        $prodarray["Payment_Reference_Number1"] = $orderitem["order_item_identifier"];
        $prodarray["Exchange"]                  = env('DOLLAR_RATE');
        $prodarray["Nature"]                    = "Import";

        // $prodarray["International_Shipment_ID"] = isset($prodarray["courier_awb"])?$prodarray["courier_awb"]:'';
        // //$prodarray["Bombino_Shipment_ID"] = isset($prodarray["courier_awb"])?$prodarray["courier_awb"]:'';
        // $prodarray["International_Courier_Name"] = isset($prodarray["courier_name"])?$prodarray["courier_name"]:'';
        // $prodarray["US_Shipper"] = $prodarray["us_shipper"];

        // //$prodarray["US_Shipper"] = "Sabs Infotech";

        return $prodarray;

        // $leads = array($prodarray);
    }
}
