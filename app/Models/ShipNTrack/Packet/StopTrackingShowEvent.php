<?php

namespace App\Models\ShipNTrack\Packet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StopTrackingShowEvent extends Model
{
    use HasFactory;

    protected $connection = 'shipntracking';
    protected $fillable = [
        'forwarder_code',
        'tracking_status',
        'event',
        'show_tracking',
        'stop_tracking',
        'status'
    ];
}
