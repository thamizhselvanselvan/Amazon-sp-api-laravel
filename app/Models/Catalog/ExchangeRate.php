<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
    protected $fillable = [
        'source_destination',
        'base_weight',
        'base_ship_charge',
        'packaging',
        'seller_commission',
        'duty_rate',
        'selling_price_commission',
        'excerise_rate',
        'amazon_commission',
    ];
}
