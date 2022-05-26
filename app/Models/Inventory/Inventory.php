<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $fillable = ['asin','item_name','quantity'];
    protected $table = "inventory";

   public function warehouses() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
    
}
