<?php

use App\Services\Zoho\ZohoOrderFormat;
use App\Models\order\OrderItemDetails;
use App\Models\order\OrderUpdateDetail;

class ZohoOrderPreview {

    public function __construct(public ZohoOrderFormat $zoho_order_format)
    {
    }

    public function index($amazon_order_id = null)
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

            $store_name = $this->zoho_order_format->get_store_name($order_item_details->store_details);
            $country_code = $this->zoho_order_format->get_country_code($order_item_details->store_details);

            $prod_array['data'] = $this->zoho_order_format->zohoOrderFormating($order_item_details, $store_name, $country_code, $orderItems);
        }

        return $prod_array;
    }
}