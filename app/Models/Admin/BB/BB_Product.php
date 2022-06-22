<?php

namespace App\Models\Admin\BB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BB_Product extends Model
{
    use HasFactory;
    protected $connection = 'buybox';
    protected $table = 'products';
    
    protected $fillable = [
        'seller_id',
        'sync_status',
        'delist',
        'item_name',
        'item_description',
        'listing_id',
        'seller_sku',
        'price',
        'quantity',
        'open_date',
        'image_url',
        'item_is_marketplace',
        'product_id_type',
        'zshop_shipping_fee',
        'item_note',
        'item_condition',
        'zshop_category1',
        'zshop_browse_path',
        'zshop_storefront_feature',
        'asin1',
        'asin2',
        'asin3',
        'country_code',
        'will_ship_internationally',
        'expedited_shipping',
        'zshop_boldface',
        'product_id',
        'bid_for_featured_placement',
        'add_delete',
        'pending_quantity',
        'fulfillment_channel',
        'business_price',
        'quantity_price_type',
        'quantity_power_bound_1',
        'quantity_price_1',
        'quantity_lower_bound_2',
        'quantity_price_2',
        'quantity_lower_bound_3',
        'quantity_price_3',
        'quantity_lower_bound_4',
        'quantity_price_4',
        'quantity_lower_ound_5',
        'quantity_price_5',
        'optional_payment_type_exclusion',
        'merchant_shipping_group',
        'scheduled_delivery_sku_set',
        'standard_price_point',
        'product_tax_code',
        'status',
        'progressive_price_type',
        'progressive_lower_bound_1',
        'progressive_price_1',
        'progressive_lower_bound_2',
        'progressive_price_2',
        'progressive_lower_bound_3',
        'progressive_price_3',
        'minimum_seller_allowed_price',
        'maximum_seller_allowed_price',
        'maximum_retail_price',
        'min_seller_price',
        'base_price',
        'ceil_price'
    ];

}
