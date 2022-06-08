<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $table = "inventory";

    protected $fillable = ['ship_id', 'asin','item_name','price','quantity','bin_id'];

    public function warehouses() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function shipment() {
        return $this->hasOne(Shipment::class, 'ship_id', 'ship_id');
    }
    
}
