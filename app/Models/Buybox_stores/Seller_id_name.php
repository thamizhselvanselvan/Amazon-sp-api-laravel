<?php

namespace App\Models\Buybox_stores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller_id_name extends Model
{
    use HasFactory;

    protected $connection = 'buybox';

    protected $fillable = [
        'seller_store_id',
        'seller_name',
        'mws_region_id'
    ];
    
}
