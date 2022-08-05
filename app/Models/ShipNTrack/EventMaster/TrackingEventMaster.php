<?php

namespace App\Models\ShipNTrack\EventMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEventMaster extends Model
{
    use HasFactory;
    
    protected $connection = 'shipntracking';
    protected $table = 'tracking_event_masters';

    protected $fillable = [
        'event_code',
        'description',
        'active',
    ];
}
