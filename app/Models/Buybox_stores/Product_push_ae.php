<?php

namespace App\Models\Buybox_stores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_push_ae extends Model
{
    use HasFactory;
    protected $connection = 'buybox_stores';
    protected $table = 'products_push_aes';


    protected $fillable = [
        'asin',
        'product_sku',
        'store_id',
        'availability',
        'push_price',
        'base_price',
        'app_360_price',
        'destination_bb_price',
        'latency',
        'applied_rules',
        'current_store_price',
        'lowest_seller_id',
        'lowest_seller_price',
        'highest_seller_id',
        'highest_seller_price',
        'bb_winner_id',
        'bb_winner_price',
        'is_bb_won',
        'push_status',
        'feedback_price_id',
        'feedback_response',
        'feedback-availability_id',
        'feedback_price_status',
        'ceil_price'
    ];
}
