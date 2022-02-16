<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class universalTextile extends Model
{
    use HasFactory;
    
    protected $fillable = [

        'textile_id',
        'ean',
        'brand',
        'title',
        'size',
        'color',
        'transfer_price',
        'shipping_weight',
        'product_type',
        'quantity'
    ];
}
