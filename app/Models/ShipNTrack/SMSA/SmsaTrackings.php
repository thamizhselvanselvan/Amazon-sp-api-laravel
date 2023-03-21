<?php

namespace App\Models\ShipNTrack\SMSA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsaTrackings extends Model
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
