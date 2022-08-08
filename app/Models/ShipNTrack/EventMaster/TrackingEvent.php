<?php

namespace App\Models\ShipNTrack\EventMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;
    
    protected $connection = 'shipntracking';
    protected $fillable = [
        'event_code',
        'description',
        'active',
    ];
}
