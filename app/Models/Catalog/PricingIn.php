<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingIn extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
    protected $fillable = [
        'asin',
        'available',
        'in_price',
        'weight',
        'ind_to_uae',
        'ind_to_sg',
        'ind_to_sa',
        'price_updated_at'
    ];
}
