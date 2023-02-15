<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingAe extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
    protected $fillable = [
        'asin',
        'available',
        'is_sold_by_amazon',
        'weight',
        'volumetric_weight_pounds',
        'volumetric_weight_kg',
        'ae_price',
        'next_highest_seller_price',
        'next_highest_seller_id',
        'next_lowest_seller_price',
        'next_lowest_seller_id',
        'bb_winner_price',
        'bb_winner_id',
        'is_any_our_seller_won_bb',
        'price_updated_at'
    ];
}
