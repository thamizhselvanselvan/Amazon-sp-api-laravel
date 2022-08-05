<?php

namespace App\Models\ShipNTrack\Packet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacketForwarder extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $fillable = [
        'order_id',
        'awb_no',
        'forwarder_1',
        'forwarder_1_awb',
        'forwarder_2',
        'forwarder_2_awb'
    ];
}
