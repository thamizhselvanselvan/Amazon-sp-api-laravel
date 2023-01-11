<?php

namespace App\Models\order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $table = 'orders';
    protected $fillable = [
        'our_seller_identifier',
        'country',
        'source',
        'amazon_order_identifier',
        'purchase_date',
        'last_update_date',
        'order_status',
        'fulfillment_channel',
        'sales_channel',
        'ship_service_level',
        'order_total',
        'number_of_items_shipped',
        'number_of_items_unshipped',
        'payment_method',
        'payment_method_details',
        'marketplace_identifier',
        'shipment_service_level_category',
        'order_type',
        'earliest_ship_date',
        'latest_ship_date',
        'earliest_delivery_date',
        'latest_delivery_date',
        'is_business_order',
        'is_prime',
        'is_premium_order',
        'is_global_express_enabled',
        'is_replacement_order',
        'is_sold_by_ab',
        'default_ship_from_location_address',
        'is_ispu',
        'shipping_address',
        'buyer_info',
        'automated_shipping_settings',
        'order_item',
        'updated_at',
        'created_at',
        'seller_order_identifier',
        'is_access_point_order',
        'has_regulated_items',
        'easy_ship_shipment_status',
        'payment_execution_detail',
        'replaced_order_identifier'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('');
    }

    public function getBuyerInfoAttribute($value)
    {
        return json_decode($this->attributes['buyer_info'], true);
    }
}
