<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment_Inward_Details extends Model
{
    protected $connection = 'inventory';
    protected $table = 'shipment_inward_details';
    protected $fillable = [
        'ship_id',
        'warehouse_id',
        'source_id',
        'currency',
        'asin',
        'item_name',
        'price',
        'quantity',
        'out_quantity',
        'balance_quantity',
        'bin'
    ];
    public function warehouses() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
    public function vendors() {
        return $this->hasOne(Vendor::class, 'id', 'source_id');
    }
}
