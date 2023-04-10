<?php

namespace App\Models\ShipNTrack\Courier;

use App\Models\ShipNTrack\Booking;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShipNTrack\Courier\Courier;
use App\Models\ShipNTrack\Courier\CourierPartner;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusManagement extends Model
{
    use HasFactory;

    protected $connection = 'shipntracking';
    protected $table = 'status_master';
    protected $fillable = [
        'courier_id',
        'courier_status',
        'booking_master_id',
        'stop_tracking',
        'api_display',
    ];

    public function courierpartner()
    {
        return $this->hasOne(CourierPartner::class, 'id', 'courier_id');
    }
    public function courierstatus()
    {
        return $this->hasOne(Courier::class, 'id', 'courier_id');
    }

}
