<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $fillable = ['source_id','asin','ship_id','item_name','quantity','price','currency','warehouse'];

    public function vendors() {
        return $this->hasOne(Vendor::class, 'id', 'source_id');
    }
    public function warehouses() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse');
    }
}
