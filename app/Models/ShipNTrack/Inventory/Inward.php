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
        'shipment_id',
        'total_items_in_export',
        'total_items_receved',
        'international_awb_number',
    ];
}
