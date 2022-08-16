<?php

namespace App\Models\ShipNTrack\Bombino;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BombinoTracking extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $fillable = [
        'awbno',
        'consignee',
        'destination',
        'forwarding_no',
        'hawb_no',
        'origin',
        'ship_date',
        'status',
        'weight',
    ];

    public function bombinoTrackingJoin()
    {
        return $this->hasMany(BombinoTrackingDetails::class, 'awbno', 'awbno');
    }

    
}
