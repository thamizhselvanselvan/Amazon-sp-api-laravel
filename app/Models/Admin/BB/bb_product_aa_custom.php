<?php

namespace App\Models\Admin\BB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bb_product_aa_custom extends Model
{
    use HasFactory;
    protected $connection = 'buybox';
    protected $fillable = [
        'seller_id',
        'active',
        'seller_sku',
        'price',
        'asin1',
        'item_name',
        'quantity',
        'item_condition',
        'fulfullment_channel',
        'base_price',
        'ceil_price',
    ];
}
