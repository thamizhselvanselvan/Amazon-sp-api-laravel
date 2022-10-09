<?php

namespace App\Models\ShipNTrack\Courier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierPartner extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $fillable = [
        'name',
        'source_destination',
        'courier_code',
        'active'
    ];
}
