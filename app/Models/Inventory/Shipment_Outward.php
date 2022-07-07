<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment_Outward extends Model
{ 
    protected $connection = 'inventory';
    protected $table = 'shipments_outward';
    protected $fillable = [
        'warehouse_id',
        'destination_id',
        'ship_id',
        'currency',
        'shipment_count',
    ];
    
}
  
