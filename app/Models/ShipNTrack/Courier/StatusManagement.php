<?php

namespace App\Models\ShipNTrack\Courier;

use App\Models\ShipNTrack\Booking;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShipNTrack\Courier\CourierPartner;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusManagement extends Model
{
    use HasFactory;

    protected $connection = 'shipntracking';
    protected $table = 'courier_status_masters';
    protected $fillable = [
        'courier_partner_id',
        'courier_partner_status',
        'booking_master_id',
        'stop_tracking',
    ];

    public function courierpartner()
    {
        return $this->hasOne(CourierPartner::class, 'id', 'courier_partner_id');
    }
    // public function bookingmaster()
    // {
    //     return $this->hasOne(Booking::class, 'id', 'booking_master_id');
    // }

}
