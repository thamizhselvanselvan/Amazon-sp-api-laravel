<?php

namespace App\Models\ShipNTrack\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\ShipNTrack\Courier\CourierPartner;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manifest_Item extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'manifest_items';
    
    protected $fillable = [
        'manifest_id',
        'awb',
        'destination',
        'inscan_manifest_id',
        'order_id',
        'purchase_tracking_id',
        'forwarder_1',
        'forwarder_1_awb',
        'international_awb_number',
    ];
      public function CourierPartner1()
    {
        return $this->hasOne(CourierPartner::class, 'id', 'forwarder_1');
    }

}
