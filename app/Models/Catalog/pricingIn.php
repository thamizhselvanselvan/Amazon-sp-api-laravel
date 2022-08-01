<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pricingIn extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
    protected $fillable = [
        'asin',
        'weight',
        'uae_price',
        'sg_sp',
        'sa_sp',
        'price_updated_at'
    ];
}
