<?php

namespace App\Models\order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class US_Price_Missing extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $table = 'us_price_missing';

    protected $fillable = [
        'country_code',
        'title',
        'asin',
        'amazon_order_id',
        'order_item_id',
        'price',
        'status',

    ];
}
