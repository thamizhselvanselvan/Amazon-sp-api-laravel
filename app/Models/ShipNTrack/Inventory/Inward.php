<?php

namespace App\Models\ShipNTrack\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inward extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'inwardings';
    protected $fillable = [
        'manifest_id',
        'mode',
        'type',
        'awb_number',
        'status'
    ];
}
