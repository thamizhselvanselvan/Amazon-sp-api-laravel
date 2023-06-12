<?php

namespace App\Services\Zoho;

use in;
use Str;
use Carbon\Carbon;
use App\Models\order\Order;
use App\Services\Zoho\ZohoApi;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_us;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\order\OrderItemDetails;
use App\Services\Zoho\ZohoOrderFormat;
use App\Models\order\OrderUpdateDetail;

class ZohoOrder
{
    public $zoho_order_format;
    public $zohoApi;

    public function __construct()
    {
        $this->zoho_order_format = new ZohoOrderFormat;
        $this->zohoApi = new ZohoApi(new_zoho: false);
    }

    public function index($amazon_order_id = null, $force_update = null)
    {
        $notes = [];

        $order_items = $this->get_orders_with_amazon_order_id($amazon_order_id);

        if (!$order_items) {
            $notes['notes'][] = "No data found to update Zoho in the database for this Amazon Order ID: $amazon_order_id";

            return $notes;
        }

        if ($order_items && $order_items->zoho_id) {
            $notes['notes'][] = "Zoho ID for Amazon Order id $amazon_order_id is already created";

            return $notes;
        }
      
        $amazon_order_id = ($amazon_order_id) ? $amazon_order_id : $order_items->amazon_order_id;
        $order_item_identifier = isset($order_items->order_item_id) ? $order_items->order_item_id : null;

        $order_details = [
            "orderitemdetails.seller_identifier",
            "orderitemdetails.asin",
            "orderitemdetails.seller_sku",
            "orderitemdetails.title",
            "orderitemdetails.order_item_identifier",
            "orderitemdetails.quantity_ordered",
            "orderitemdetails.item_price",
            "orderitemdetails.item_tax",
            "orderitemdetails.shipping_address",

            "orders.fulfillment_channel",
            "orders.our_seller_identifier",
            "orders.amazon_order_identifier",
            "orders.purchase_date",
            "orders.earliest_delivery_date",
            "orders.buyer_info",
            "orders.order_total",
            "orders.latest_delivery_date",
            "orders.is_business_order",
        ];

      
        $order_item_details = OrderItemDetails::select($order_details)
            ->join('orders', 'orderitemdetails.amazon_order_identifier', '=', 'orders.amazon_order_identifier')
            ->where('orderitemdetails.amazon_order_identifier', $amazon_order_id)
            ->when($order_item_identifier, function ($query, $role) {
                return $query->where('order_item_identifier', $role);
            })
            ->with(['store_details.mws_region'])
            ->limit(1)
            ->first();

        if ($order_item_details) {
            $type = 'old method';
            $order_item_id = $order_item_details->order_item_identifier;
            $zoho_search_order_exists = $this->zohoApi->search($amazon_order_id, $order_item_id, $type);

            $store_name = $this->zoho_order_format->get_store_name($order_item_details->store_details);
            $country_code = $this->zoho_order_format->get_country_code($order_item_details->store_details);

            $prod_array = $this->zoho_order_format->zohoOrderFormating($order_item_details, $store_name, $country_code, $order_items);

            //update zoho status to 3 if not item name
            if (!$prod_array) {

                //slack Notification 
                $slackMessage = 'Name or Address is missing ' .
                    'Store Name = ' . $store_name .' '.
                    'Store ID = ' . $order_item_details->seller_identifier .' '.
                    'Amazon Order ID = ' . $amazon_order_id . ' ' .
                    'Order Item Identifier = ' .  $order_item_id;

                slack_notification('app360', 'Zoho Booking', $slackMessage);

                return OrderUpdateDetail::query()
                    ->where([
                        'order_item_id' => $order_item_id,
                        'amazon_order_id' => $amazon_order_id
                    ])
                    ->update(['zoho_status' => 5]);
            }

            if ($zoho_search_order_exists && $force_update) {

                return $this->zoho_force_update($zoho_search_order_exists, $prod_array);
            } else if ($zoho_search_order_exists && !$force_update) {

                return $this->zoho_update($zoho_search_order_exists, $order_item_details, $prod_array, $amazon_order_id, $order_item_id);
            } else if (!$zoho_search_order_exists) {

                return $this->zoho_save($prod_array, $order_item_details, $amazon_order_id, $order_item_id);
            }

            $notes['notes'][] = "Zoho Already exists with Amazon Order ID: {$amazon_order_id}, Order Item ID: {$order_item_id}";

            return $notes;
        }

        $notes['notes'][] = "Catalog Item details did not get";

        return $notes;
    }

    public function get_orders_with_amazon_order_id($amazon_order_id)
    {
        $orderItems = OrderUpdateDetail::query()
            ->when($amazon_order_id, function ($query, $role) {
                return $query->where('amazon_order_id', $role);
            })
            ->where("zoho_status", 0)
            ->whereNull('zoho_id')
            ->where('courier_name', 'B2CShip')
            ->whereNotNull('courier_awb')
            ->where('booking_status', 1)
            ->limit(1)
            ->first();

        if (!$orderItems) {
            $orderItems = OrderUpdateDetail::query()
                ->when($amazon_order_id, function ($query, $role) {
                    return $query->where('amazon_order_id', $role);
                })
                ->whereNull('courier_name')
                ->where("zoho_status", 0)
                ->whereNull('zoho_id')
                ->limit(1)
                ->first();
        }

        if ($orderItems) {

            OrderUpdateDetail::query()
                ->where('amazon_order_id', $orderItems->amazon_order_id)
                ->where('order_item_id', $orderItems->order_item_id)
                ->update(['zoho_status' => 5]);

            return $orderItems;
        }

        return false;
    }

    public function zoho_save($prod_array, $order_item_details, $amazon_order_id, $order_item_id)
    {
        $type = 'old method';
        $zoho_search_order_exists = $this->zohoApi->search($amazon_order_id, $order_item_id, $type);

        if ($zoho_search_order_exists) {

            return $this->zoho_update($zoho_search_order_exists, $order_item_details, $prod_array, $amazon_order_id, $order_item_id);
        }

        $type = 'old method';
        $zoho_api_save = $this->zohoApi->storeLead($prod_array, $type);

        $zoho_response = ($zoho_api_save) ? $zoho_api_save : null;

        if (isset($zoho_response) && gettype($zoho_response) == "array" && array_key_exists('data', $zoho_response) && array_key_exists(0, $zoho_response['data']) && array_key_exists('code', $zoho_response['data'][0])) {

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
                $notes['lead_id'] = $zoho_save_id;

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

    public function zoho_update($zoho_search_order_exists, $order_item_details, $prod_array, $amazon_order_id, $order_item_id)
    {

        $zoho_lead_id = $zoho_search_order_exists['data'][0]['id'];
        $notes['notes'][] = "With this Amazon Order ID: $amazon_order_id & Order Item ID: $order_item_id already Zoho Lead exists. Lead ID: " . $zoho_lead_id;

        $order_zoho = [
            "store_id" => $order_item_details->seller_identifier,
            "amazon_order_id" => $amazon_order_id,
            "order_item_id" => $prod_array['Payment_Reference_Number1'],
            "zoho_id" => $zoho_lead_id,
            "zoho_status" => 1
        ];

        OrderUpdateDetail::upsert(
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

        return $notes;
    }

    public function zoho_force_update($zoho_search_order_exists, $prod_array)
    {
        $notes = [];
        $zoho_lead_id = $zoho_search_order_exists['data'][0]['id'];
        $amazon_order_id = $zoho_search_order_exists['data'][0]['Alternate_Order_No'];
        $order_item_id = $zoho_search_order_exists['data'][0]['Payment_Reference_Number1'];
        $type = 'old method';
        $zoho_response =  $this->zohoApi->updateLead($zoho_lead_id, $prod_array, $type);

        if (isset($zoho_response) && array_key_exists('data', $zoho_response) && array_key_exists(0, $zoho_response['data']) && array_key_exists('code', $zoho_response['data'][0]) && $zoho_response['data'][0]['code'] == "SUCCESS") {
            $notes['notes'][] = "Amazon Order id: $amazon_order_id with Order Item ID: $order_item_id updated in Zoho successful";
        } else {
            $notes['notes'][] = "Amazon Order id: $amazon_order_id with Order Item ID: $order_item_id updated in Zoho failed";
        }

        return $notes;
    }
}
