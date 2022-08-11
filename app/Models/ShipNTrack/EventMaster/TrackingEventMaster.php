<?php

namespace App\Models\ShipNTrack\EventMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ShipNTrack\EventMapping\TrackingEventMapping;

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

    // public function TrackingEventMapping(){
        
    //     return $this->belongsTo(TrackingEventMapping::class, 'event_code', 'master_event_code');
    // }
}
