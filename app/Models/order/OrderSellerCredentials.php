<?php

namespace App\Models\order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSellerCredentials extends Model
{
    use HasFactory;
    protected $fillable = [

        'seller_id',
        'mws_region_id',
        'store_name',
        'merchan_id',
        'auth_code', 
        'dump_order',
    ];
}
