<?php

namespace App\Models\order;

use App\Models\Aws_credential;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemDetails extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $table = 'orderitemdetails';
    protected $fillable = [
        'seller_identifier',
        'status',
        'country',
        'source',
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
        'buyer_requested_cancel'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('');
    }

    public function getShippingAddressAttribute($value)
    {
        return json_decode($this->attributes['shipping_address'], true);
    }

    public function store_details()
    {
        return $this->hasOne(Aws_credential::class, 'seller_id', 'seller_identifier');
    }

    // public function orders()
    // {
    //     return $this->hasOne(Order::class, 'amazon_order_identifier', 'amazon_order_identifier');
    // }
}
