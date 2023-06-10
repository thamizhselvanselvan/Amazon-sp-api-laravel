<?php

namespace App\Models\ShipNTrack\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outwarding extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'outwardings';
    protected $fillable = [
        'order_id',
        'mode',
        'purchase_tracking_id',
        'awb_number',
        'forwarder_2',
        'forwarder_2_awb',
    ];
}
