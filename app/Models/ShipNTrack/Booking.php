<?php

namespace App\Models\ShipNTrack;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $connection = 'shipntracking';
    protected $table = 'booking_masters';
    protected $fillable = [
        'name'
    ];
}
