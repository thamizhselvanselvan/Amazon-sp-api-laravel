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
        'source',
        'destination',
        'courier_code',
        'active',
        'type',
        'key1',
        'key2',
        'key3',
        'key4',
        'key5'
    ];
}
