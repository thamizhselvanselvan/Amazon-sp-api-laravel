<?php

namespace App\Models\order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderUpdateDetail extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $fillable = [
        'store_id',
        'amazon_order_id',
        'order_item_id',
        'courier_name',
        'courier_awb',
        'zoho_id',
        'zoho_order_id',
        'amzn_temp_order_status',
    ];
}
