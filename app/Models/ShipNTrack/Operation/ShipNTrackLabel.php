<?php

namespace App\Models\ShipNTrack\Operation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipNTrackLabel extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'labels';
    protected $fillable = [
        'order_no',
        'order_item_id',
        'bag_no',
        'forwarder',
        'awb_no',
        'order_date',
        'customer_name',
        'address',
        'city',
        'county',
        'country',
        'phone',
        'product_name',
        'sku',
        'quantity'
    ];
}
