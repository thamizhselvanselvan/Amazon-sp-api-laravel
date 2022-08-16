<?php

namespace App\Models\ShipNTrack\Packet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StopPacketTracking extends Model
{
    use HasFactory;

    protected $connection = 'shipntracking';
    protected $fillable = [
        'forwarder',
        'tracking_status'
    ];
}
