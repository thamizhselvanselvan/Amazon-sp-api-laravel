<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingSa extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
    protected $fillable = [
        'asin',
        'weight',
        'available',
        'sa_price',
        'price_updated_at'
    ];
}
