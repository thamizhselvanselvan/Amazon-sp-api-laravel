<?php

namespace App\Services\AWS_Nitshop;

use Illuminate\Support\Facades\DB;

class Index
{

    public function order()
    {

        DB::connection('order_no_prefix')->table("orders")->chunkById(1000, function ($orders) {

            $order_all = [];
            foreach ($orders as $order) {
                $order_all[] = (array)$order;
            }

            DB::connection('aws')
                ->table("app_360_orders")
                ->upsert(
                    $order_all,
                    ['amazon_order_identifier'],
                    [
                        "our_seller_identifier",
                        "country",
                        "amazon_order_identifier",
                        "purchase_date",
                        "last_update_date",
                        "order_status",
                        "fulfillment_channel",
                        "sales_channel",
                        "ship_service_level",
                        "order_total",
                        "number_of_items_shipped",
                        "number_of_items_unshipped",
                        "payment_method",
                        "payment_method_details",
                        "marketplace_identifier",
                        "shipment_service_level_category",
                        "order_type",
                        "earliest_ship_date",
                        "latest_ship_date",
                        "earliest_delivery_date",
                        "latest_delivery_date",
                        "is_business_order",
                        "is_prime",
                        "is_premium_order",
                        "is_global_express_enabled",
                        "is_replacement_order",
                        "is_sold_by_ab",
                        "default_ship_from_location_address",
                        "is_ispu",
                        "shipping_address",
                        "buyer_info",
                        "automated_shipping_settings",
                        "order_item",
                        "updated_at",
                        "seller_order_identifier",
                        "is_access_point_order",
                        "has_regulated_items"
                    ]
                );
        });
    }

    public function order_details()
    {
        DB::connection('order_no_prefix')->table("orderitemdetails")->chunkById(1000, function ($orders) {

            $order_all = [];
            foreach ($orders as $order) {
                $order_all[] = (array)$order;
            }

            DB::connection('aws')
                ->table("app_360_order_item_details")
                ->upsert(
                    $order_all,
                    ['amazon_order_identifier'],
                    [
                        'seller_identifier',
                        'status',
                        'country',
                        'asin',
                        'seller_sku',
                        'order_item_identifier',
                        'title',
                        'quantity_ordered',
                        'quantity_shipped',
                        'product_info',
                        'points_granted',
                        'item_price',
                        'shipping_price',
                        'item_tax',
                        'shipping_tax',
                        'shipping_discount',
                        'shipping_discount_tax',
                        'promotion_discount',
                        'promotion_discount_tax',
                        'promotion_identifiers',
                        'cod_fee',
                        'cod_fee_discount',
                        'is_gift',
                        'condition_note',
                        'condition_identifier',
                        'condition_subtype_identifier',
                        'scheduled_delivery_start_date',
                        'scheduled_delivery_end_date',
                        'price_designation',
                        'tax_collection',
                        'serial_number_required',
                        'is_transparency',
                        'ioss_number',
                        'store_chain_store_identifier',
                        'deemed_reseller_category',
                        'buyer_info',
                        'amazon_order_identifier',
                        'shipping_address',
                        'updated_at',
                        'buyer_requested_cancel'
                    ]
                );
        });
    }
}
