<?php

namespace App\Models\ShipNTrack\CourierTracking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsaTracking extends Model
{
    use HasFactory;

    protected $connection = 'shipntracking';
    protected $fillable = [
        'account_id',
        'awbno',
        'date',
        'activity',
        'details',
        'location',
    ];
}
