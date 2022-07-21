<?php

namespace App\Models\ShipNTrack\Bombino;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BombinoTrackingDetails extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $fillable = [
        'awbno',
        'action_date',
        'action_time',
        'event_code',
        'event_details',
        'exception',
        'location',
    ];
}
