<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingUs extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
    protected $table = 'pricing_uss';
    protected $fillable = [
        'asin',
        'available',
        'weight',
        'us_price',
        'usa_to_in_b2b',
        'usa_to_in_b2c',
        'usa_to_uae',
        'usa_to_sg',
        'price_updated_at'
    ];
}
