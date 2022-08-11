<?php

namespace App\Models\ShipNTrack\EventMapping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ShipNTrack\EventMaster\TrackingEventMaster;

class TrackingEventMapping extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $fillable = [
        'master_event_code',
        'source',
        'our_event_code',
        'our_event_description',
        'active'
    ];

    public function TrackingEventMaster()
    {
        return $this->belongsTo(TrackingEventMaster::class, 'master_event_code', 'event_code');
    }
}
