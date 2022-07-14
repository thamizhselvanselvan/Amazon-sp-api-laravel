<?php

namespace App\Models\ShipNTrack\SMSA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsaTrackings extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'awbno',
        'date',
        'activity',
        'details',
        'location',
    ];

}
