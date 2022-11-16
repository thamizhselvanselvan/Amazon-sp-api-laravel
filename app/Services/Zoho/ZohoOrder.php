<?php

namespace App\Services\Zoho;

use in;
use Str;
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

    public function lead_preview($amazon_order_id = null)
    {
        $prod_array = [];
        $orderItems = OrderUpdateDetail::query()
            ->where('amazon_order_id', $amazon_order_id)
            ->limit(1)
            ->first();

        if (!$orderItems) {
            $prod_array['note_1'] = "No zoho id found in the database for this Amazon Order ID: $amazon_order_id";
        }

        if (isset($orderItems) && $orderItems->zoho_id) {
            $prod_array['note_2'] = "Zoho ID for Amazon Order id $amazon_order_id is already created";

            return $prod_array;
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

        $order_item_details = OrderItemDetails::query()
            ->select($order_details)
            ->join('orders', 'orderitemdetails.amazon_order_identifier', '=', 'orders.amazon_order_identifier')
            ->where('orderitemdetails.amazon_order_identifier', $amazon_order_id)
            ->with(['store_details.mws_region'])
            ->limit(1)
            ->first();

        if ($order_item_details) {

            $store_name = $this->get_store_name($order_item_details->store_details);
            $country_code = $this->get_country_code($order_item_details->store_details);

            $prod_array['data'] = $this->zohoOrderFormating($order_item_details, $store_name, $country_code);
        }

        return $prod_array;
    }

    public function index($amazon_order_id = null, $force_update = null)
    {
        $notes = [];

        $order_items = $this->get_orders_with_amazon_order_id($amazon_order_id);

        if (!$order_items) {
            Log::info($order_items);
            $notes['notes'][] = "No data found to update Zoho in the database for this Amazon Order ID: $amazon_order_id";
            return $notes;
        }

        if ($order_items && $order_items->zoho_id) {
            $notes['notes'][] = "Zoho ID for Amazon Order id $amazon_order_id is already created";
            return $notes;
        }

        $amazon_order_id = ($amazon_order_id) ? $amazon_order_id : $order_items->amazon_order_id;

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

            $order_item_id = $order_item_details->order_item_identifier;

            $zohoApi = new ZohoApi;
            $zoho_search_order_exists = $zohoApi->search($amazon_order_id, $order_item_id);

            $store_name = $this->get_store_name($order_item_details->store_details);
            $country_code = $this->get_country_code($order_item_details->store_details);

            $prod_array = $this->zohoOrderFormating($order_item_details, $store_name, $country_code);


            if ($zoho_search_order_exists && $force_update) {

                return $this->zoho_update($zohoApi, $zoho_search_order_exists, $prod_array);
            } else if ($zoho_search_order_exists && !$force_update) {
                $notes['notes'][] = "With this Amazon Order ID: $amazon_order_id & Order Item ID: $order_item_id already Zoho Lead exists";
                return $notes;
            }

            $zoho_api_save = $zohoApi->storeLead($prod_array);

            $zoho_response = ($zoho_api_save) ? $zoho_api_save : null;

            if (isset($zoho_response) && array_key_exists('data', $zoho_response) && array_key_exists(0, $zoho_response['data']) && array_key_exists('code', $zoho_response['data'][0])) {

                $zoho_save_id = $zoho_response['data'][0]['details']['id'];

                $order_zoho = [
                    "store_id" => $order_item_details->seller_identifier,
                    "amazon_order_id" => $amazon_order_id,
                    "order_item_id" => $prod_array['Payment_Reference_Number1'],
                    "zoho_id" => $zoho_save_id,
                    "zoho_status" => 1
                ];

                $order_response = OrderUpdateDetail::upsert(
                    $order_zoho,
                    [
                        "amazon_order_id",
                        "order_item_id"
                    ],
                    [
                        "zoho_id",
                        "store_id",
                        "zoho_status"
                    ]
                );

                if ($order_response) {
                    $notes['success'] = "Success!";
                    $notes['amazon_order_id'] = $amazon_order_id;
                    $notes['order_item_id'] = $prod_array['Payment_Reference_Number1'];
                    return $notes;
                } else {
                    Log::error(json_encode($zoho_response));

                    $notes['notes'][] = "While saving data error found!";
                    return $notes;
                }
            } else {
                Log::error("Zoho Response : " . json_encode($zoho_response));

                $notes['notes'][] = "Error No Response After Updating Zoho!";
                return $notes;
            }
        }

        $notes['notes'][] = "Catalog Item details did not get";

        return $notes;
    }

    public function zoho_update($zohoApi, $zoho_search_order_exists, $prod_array)
    {
        $notes = [];
        $zoho_lead_id = $zoho_search_order_exists['data'][0]['id'];
        $amazon_order_id = $zoho_search_order_exists['data'][0]['Alternate_Order_No'];
        $order_item_id = $zoho_search_order_exists['data'][0]['Payment_Reference_Number1'];

        $zoho_response = $zohoApi->updateLead($zoho_lead_id, $prod_array);

        if (isset($zoho_response) && array_key_exists('data', $zoho_response) && array_key_exists(0, $zoho_response['data']) && array_key_exists('code', $zoho_response['data'][0]) && $zoho_response['data'][0]['code'] == "SUCCESS") {
            $notes['notes'][] = "Amazon Order id: $amazon_order_id with Order Item ID: $order_item_id updated in Zoho successful";
        } else {
            $notes['notes'][] = "Amazon Order id: $amazon_order_id with Order Item ID: $order_item_id updated in Zoho failed";
        }

        return $notes;
    }


    public function get_orders_with_amazon_order_id($amazon_order_id)
    {
        $orderItems = OrderUpdateDetail::query()
            //->where('amazon_order_id', $amazon_order_id)
            ->when($amazon_order_id, function ($query, $role) {
                return $query->where('amazon_order_id', $role);
            })
            ->where("zoho_status", 0)
            ->where('zoho_id', null)
            ->where('courier_name', 'B2CShip')
            ->whereNotNull('courier_awb')
            ->where('booking_status', 1)
            ->limit(1)
            ->first();

        if (!$orderItems) {
            $orderItems = OrderUpdateDetail::query()
                //->where('amazon_order_id', $amazon_order_id)
                ->when($amazon_order_id, function ($query, $role) {
                    return $query->where('amazon_order_id', $role);
                })
                ->where("zoho_status", 0)
                ->where('zoho_id', null)
                ->limit(1)
                ->first();
        }

        // Log::info($orderItems);
        // exit;

        if ($orderItems) {
            return $orderItems;
        }

        return false;
    }


    public function index_working($amazon_order_id = null)
    {
        $notes = [];

        $orderItems = OrderUpdateDetail::where('amazon_order_id', $amazon_order_id)->limit(1)->first();

        if (!$orderItems) {
            $notes['notes'][] = "No zoho id found in the database for this Amazon Order ID: $amazon_order_id";
        }

        if (isset($orderItems) && $orderItems->zoho_id) {
            $notes['notes'][] = "Zoho ID for Amazon Order id $amazon_order_id is already created";
            return $notes;
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

            $store_name = $this->get_store_name($order_item_details->store_details);
            $country_code = $this->get_country_code($order_item_details->store_details);

            $prod_array = $this->zohoOrderFormating($order_item_details, $store_name, $country_code);

            $zohoApi = new ZohoApi;
            $zoho_api_save = $zohoApi->storeLead($prod_array);

            $zoho_response = ($zoho_api_save) ? $zoho_api_save : null;

            if (isset($zoho_response) && array_key_exists('data', $zoho_response) && array_key_exists(0, $zoho_response['data']) && array_key_exists('code', $zoho_response['data'][0])) {

                $zoho_save_id = $zoho_response['data'][0]['details']['id'];

                $order_zoho = [
                    "store_id" => $order_item_details->seller_identifier,
                    "amazon_order_id" => $amazon_order_id,
                    "order_item_id" => $prod_array['Payment_Reference_Number'],
                    "zoho_id" => $zoho_save_id
                ];

                $order_response = OrderUpdateDetail::upsert(
                    $order_zoho,
                    [
                        "amazon_order_id",
                        "order_item_id"
                    ],
                    [
                        "zoho_id",
                        "store_id"
                    ]
                );

                if ($order_response) {
                    $notes['success'] = "Success!";
                    return $notes;
                } else {
                    Log::channel('slack')->error(json_encode($zoho_response));

                    $notes['notes'][] = "While saving data error found!";
                    return $notes;
                }
            } else {
                Log::channel('slack')->error("Zoho Response : " . json_encode($zoho_response));

                $notes['notes'][] = "Error No Response After Updating Zoho!";
                return $notes;
            }
        }

        $notes['notes'][] = "Catalog Item details did not get";

        return $notes;
    }

    public function zohoOrderFormating($value, $store_name, $country_code)
    {
        $DOLLAR_EXCHANGE_RATE = 82;
        $AED_EXCHANGE_RATE = 3.8;

        $buyerDtls = (object)$value->shipping_address;

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

        $prod_array["Last_Name"]   = $buyerDtls->Name;
        $prod_array["Lead_Source"] = $this->lead_source($store_name, $country_code);
        $prod_array['Lead_Status'] = $this->lead_status($store_name, $country_code);

        if (isset($buyerDtls->AddressLine2)) {
            $address = $buyerDtls->AddressLine1 . '<br> ' . $buyerDtls->AddressLine2 ?? "";
        } else {
            $address = $buyerDtls->AddressLine1;
        }

        $address = str_replace("&", " and ", $address);

        $prod_array["Mobile"]      = substr((int) filter_var($buyerDtls->Phone, FILTER_SANITIZE_NUMBER_INT), -10);
        $prod_array["Address"]     = $address;
        $prod_array["City"]        = $buyerDtls->City;
        $prod_array['State']          = $this->get_state_pincode($country_code, $buyerDtls);
        $prod_array['Zip_Code']       = $this->get_state_pincode($country_code, $buyerDtls, 'pincode');

        $prod_array["Email"]              = ((isset($buyerEmail->BuyerEmail)) ? $buyerEmail->BuyerEmail : '');
        $prod_array["Customer_Type1"]      = ($value->is_business_order == 'true') ? 'B2B' : 'B2C';
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
        $prod_array["Amount_Paid_by_Customer"]   = (int)$item_price->Amount;

        $prod_array["Weight_in_LBS"]             = (string)$catalog_details['weight'];
        $prod_array["Payment_Reference_Number1"]  = $value->order_item_identifier;
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

        if (($store_name == 'Nitrous Stores India' && $country_code == 'IN') || ($store_name == 'MBM India' && $country_code == 'IN')) {

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
            Log::info(json_encode($buyerDtls));
            return $buyerDtls->StateOrRegion ?? $buyerDtls->County;
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

                if (isset($result) && isset($result->product_types[0]) && isset($result->product_types[0]['productType'])) {

                    $category = $result->product_types[0]['productType'];
                } else if (isset($result) && isset($result->browse_classification['displayName'])) {

                    $category = $result->browse_classification['displayName'];
                }

                break;
            }
        }

        return ['price' => $price, 'weight' => $weight, 'category' => $category];
    }
}
