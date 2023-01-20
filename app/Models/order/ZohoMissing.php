<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoMissing extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $table = 'zogo_missing';

    protected $fillable = [
        'asin',
        'amazon_order_id',
        'order_item_id',
        'status',
        'price',

    ];
}
