<?php

namespace App\Models\ShipNTrack\SMSA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KsaSmsaTracking extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'ksa_smsa_trackings';
    protected $fillable = [
        'account_id',
        'awbno',
        'date',
        'activity',
        'details',
        'location',
    ];
}
