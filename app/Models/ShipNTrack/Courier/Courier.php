<?php

namespace App\Models\ShipNTrack\Courier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'courier';
    protected $fillable = [
        'courier_name',
        'courier_code',
    ];
}
