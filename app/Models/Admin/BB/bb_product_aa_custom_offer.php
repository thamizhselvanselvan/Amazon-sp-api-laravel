<?php

namespace App\Models\Admin\BB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bb_product_aa_custom_offer extends Model
{
    use HasFactory;
    protected $connection = 'buybox';
    protected $fillable = [
        'asin',
        'cyclic',
        'delist',
        'available',
        'priority',
        'import_type',
        'total_offer_count',
        'number_of_offer',
        'buybox_eligible_offer',
        'lowestprice_condition',
        'lowestprice_fulfillment_channel',
        'lowestprice_landedprice_currency',
        'lowestprice_landedprice_amount',
        'lowestprice_listingprice_currency',
        'lowestprice_listingprice_amount',
        'lowestprice_shippping_currency',
        'lowestprice_shipping_amount',
        'buybox_condition',
        'buybox_fulfillment_channel',
        'buybox_landedprice_currency',
        'buybox_landedprice_amount',
        'buybox_listingprice_currency',
        'buybox_listingprice_amount',
        'buybox_shipping_currency',
        'buybox_shipping_amount',
    ];
}
