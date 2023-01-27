<?php

namespace App\Models\Buybox_stores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Push extends Model
{
    use HasFactory;
    protected $connection = 'buybox_stores';
    protected $table = 'product_push';

    protected $fillable = [
        'asin',
        'store_id',
        'availability',
        'product_sku',
        'push_price',
        'base_price',
        'push_status',
        'latency',
        'feedback_id',
        'feedback_response'
    ];
}
