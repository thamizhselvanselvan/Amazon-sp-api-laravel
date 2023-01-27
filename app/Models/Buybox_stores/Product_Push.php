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
        'product_sku',
        'store_id',
        'availability',
        'push_price',
        'base_price',
        'latency',
        'push_status',
        'feedback_price_id',
        'feedback_response',
        'feedback-availability_id'
    ];
}
