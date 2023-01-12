<?php

namespace App\Models\Buybox_stores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $connection = 'buybox_stores';
    
    protected $fillable = [
        'store_id', 'asin', 'cyclic', 'priority', 'availability', 'latency', 'base_price',
        'ceil_price', 'app_360_price', 'bb_price', 'push_price', 'store_price'
    ];
}
