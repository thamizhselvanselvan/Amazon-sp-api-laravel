<?php

namespace App\Models\ShipNTrack\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inwarding_detail extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'inwarding_details';
    protected $fillable = [
        'master_ref_id',
        'shipment_id',
        'mode',
        'total_items_in_export',
        'total_items_receved',
        'international_awb_number',
        'purchase_tracking_id',
        'awb_number',
        'order_id',
        'item_received_status',
        'outward_status',
    ];
}
