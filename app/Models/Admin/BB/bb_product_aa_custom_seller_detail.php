<?php

namespace App\Models\Admin\BB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bb_product_aa_custom_seller_detail extends Model
{
    use HasFactory;
    protected $connection = 'buybox';
    protected $fillable = [
        'asin',
        'seller_store_id',
        'is_buybox_winner',
        'sub_condition',
        'seller_positive_feedback_rating',
        'feedback_count',
        'is_fulfilled_by_amazon',
        'is_featured_merchant',
        'prime_information_isprime',
        'prime_information_isnational_prime',
        'ships_from_country',
        'shipping_currency_code',
        'shipping_amount',
        'listingprice_currency_code',
        'listingprice_amount',
        'landingprice_currency_code',
        'landingprice_amount',
        'minimum_hours',
        'maximum_hours',
        'availability_type',
    ];
}
