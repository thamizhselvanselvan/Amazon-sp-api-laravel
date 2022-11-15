<?php

namespace App\Models\order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSellerCredentials extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $fillable = [
        'seller_id',
        'country_code',
        'mws_region_id',
        'store_name',
        'merchan_id',
        'auth_code',
        'dump_order',
        'get_order_item',
        'enable_shipntrack',
        'courier_partner',
        'zoho',
        'source',
        'destination'
    ];
}
