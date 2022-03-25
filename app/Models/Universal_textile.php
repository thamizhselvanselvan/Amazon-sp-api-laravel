<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Universal_textile extends Model
{
    use HasFactory;

    protected $table = 'universal_textiles';

    protected $fillable = [
        'textile_id',
        'ean',
        'brand',
        'title',
        'size',
        'color',
        'quantity',
        'transfer_price',
        'shipping_weight',
        'product_type',
    ];
}
