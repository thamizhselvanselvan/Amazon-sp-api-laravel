<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $connection = 'business';
    protected $fillable = [
        'xml_sent',
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
