<?php

namespace App\Models\ShipNTrack\CourierTracking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SPLTracking extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table =  "spl_trackings";
    protected $fillable = [
        'airwaybill',
        'eventCode',
        'event',
        'eventName',
        'supplier',
        'userName',
        'notes',
        'actionDate',
        'eventCountry',
        'eventCity',
        'eventSubCode',
        'eventSubName'
    ];
}