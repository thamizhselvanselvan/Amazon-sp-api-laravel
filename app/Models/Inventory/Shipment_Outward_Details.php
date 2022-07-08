<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment_Outward_Details extends Model
{
    protected $connection = 'inventory';
    protected $table = 'shipments_outward_details';
    protected $fillable = [
        'ship_id',
        'warehouse_id',
        'destination_id',
        'currency',
        'asin',
        'item_name',
        'price',
        'quantity',
    ];
    public function vendors() {
        return $this->hasOne(Vendor::class, 'id', 'destination_id');
    }

    public function warehouses() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    } 
    
}
