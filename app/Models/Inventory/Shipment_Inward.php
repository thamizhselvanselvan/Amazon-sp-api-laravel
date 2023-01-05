<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment_Inward extends Model
{
    protected $connection = 'inventory';
    protected $table = 'shipments_inward';
    protected $fillable = [
        'warehouse_id',
        'source_id',
        'ship_id',
        'currency',
        'shipment_count',
        'inwarded_at'
    ];
}
