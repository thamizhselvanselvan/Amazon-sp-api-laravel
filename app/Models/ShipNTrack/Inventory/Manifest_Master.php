<?php

namespace App\Models\ShipNTrack\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manifest_Master extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'manifest_masters';
    protected $fillable = [
        'manifest_id',
        'total_items',
        'international_awb_number',

    ];
}
