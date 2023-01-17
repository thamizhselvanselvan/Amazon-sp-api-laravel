<?php

namespace App\Models\buybox_stores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Push extends Model
{
    use HasFactory;
    protected $connection = 'buybox_stores';
    protected $table = 'product_push';

    protected $fillable = [
        'store_id',
        'push_price',
        'latency',
        'feedback_id',
        'feedback_response'
    ];
}
