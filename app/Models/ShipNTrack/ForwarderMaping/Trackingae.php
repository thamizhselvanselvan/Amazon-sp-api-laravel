<?php

namespace App\Models\ShipNTrack\ForwarderMaping;

use Illuminate\Database\Eloquent\Model;
use App\Models\ShipNTrack\Courier\Courier;
use App\Models\ShipNTrack\Courier\CourierPartner;
use App\Models\ShipNtrack\Process\Process_Master;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trackingae extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'tracking_aes';
    protected $fillable = [
        'awb_no',
        'mode',
        'reference_id',
        'consignor_details',
        'consignee_details',
        'packet_details',
        'shipping_details',
        'booking_details',
        'forwarder_1',
        'forwarder_1_awb',
        'forwarder_1_flag',
        'forwarder_2',
        'forwarder_2_awb',
        'forwarder_2_flag',
        'forwarder_3',
        'forwarder_3_awb',
        'forwarder_3_flag',
        'forwarder_4',
        'forwarder_4_awb',
        'forwarder_4_flag',
        'status',
        'purchase_tracking_id'
    ];

    public function CourierPartner1()
    {
        return $this->hasOne(CourierPartner::class, 'id', 'forwarder_1');
    }

    public function CourierPartner2()
    {
        return $this->hasOne(CourierPartner::class, 'id', 'forwarder_2');
    }

    public function CourierPartner3()
    {
        return $this->hasOne(CourierPartner::class, 'id', 'forwarder_3');
    }

    public function CourierPartner4()
    {
        return $this->hasOne(CourierPartner::class, 'id', 'forwarder_4');
    }
}
