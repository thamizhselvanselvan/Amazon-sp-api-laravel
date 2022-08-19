<?php

namespace App\Models\Admin\BB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BB_Product extends Model
{
    use HasFactory;
    protected $connection = 'buybox';
    // protected $table = 'products';

    protected $fillable = [
        'seller_id',
        'cyclic',
        'delist',
        'active',
        'available',
        'priority',
        'seller_sku',
        'price',
        'asin1',
        'item_name',
        'quantity',
        'item_condition',
        'fulfillment_channel',
        'base_price',
        'ceil_price'
    ];
}
