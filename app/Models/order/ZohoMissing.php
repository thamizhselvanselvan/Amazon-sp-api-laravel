<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoMissing extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $table = 'zoho_missing';

    protected $fillable = [
        'country_code',
        'title',
        'asin',
        'amazon_order_id',
        'order_item_id',
        'status',
        'price',

    ];
}
