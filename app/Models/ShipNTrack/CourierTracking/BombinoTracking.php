<?php

namespace App\Models\ShipNTrack\CourierTracking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BombinoTracking extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $fillable = [
        'awb_no',
        'consignee',
        'consignor',
        'destination',
        'hawb_no',
        'origin',
        'ship_date',
        'weight',
        'action_date',
        'action_time',
        'event_code',
        'event_detail',
        'exception',
        'location'
    ];

    public function bombinoTrackingJoin()
    {
        return $this->hasMany(BombinoTrackingDetails::class, 'awbno', 'awbno');
    }
}
