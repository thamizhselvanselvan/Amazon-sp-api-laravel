<?php

namespace App\Models\order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business_Order extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $fillable = [

        'sent_payload',
        'organization_name',
        'order_date',
        'name',
        'e-mail',
        'country_name',
        'country_code',
        'order_id',
        'item_details',
        'ship_address',
        'bill_address',
        'responce_payload',
        'responce_text',
        'responce_code',
    ];
}
