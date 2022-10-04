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
        'weight',
        'us_price',
        'ind_sp',
        'uae_sp',
        'sg_sp',
        'price_updated_at'
    ];
}
