<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $table = "inventory";

    protected $fillable = ['ship_id','warehouse_id','item_name', 'asin','out_quantity','balance_quantity','price','quantity','bin_id'];

    public function warehouses() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function shipment() {
        return $this->hasOne(Shipment::class, 'ship_id', 'ship_id');

    }
    public function vendors() {
        return $this->hasOne(Vendor::class, 'id', 'source_id');
    }
}
