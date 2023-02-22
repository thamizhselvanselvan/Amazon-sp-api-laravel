<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $table = "inventory";

    protected $fillable = [
        'inventory_id',
        'ship_id',
        'warehouse_id',
        'item_name',
        'source_id',
        'ss_id',
        'tag',
        'asin',
        'out_quantity',
        'balance_quantity',
        'price',
        'inwarded_at',
        'quantity',
        'bin',
        'procurement_price'
    ];

    public function warehouses()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'ship_id', 'ship_id');
    }
    public function vendors()
    {
        return $this->hasOne(Vendor::class, 'id', 'source_id');
    }
    public function bins()
    {
        return $this->hasOne(Bin::class, 'bin_id', 'bin');
    }
    public function shelves()
    {
        return $this->hasOne(Shelve::class, 'shelve_id', 'bin');
    }
    public function tags()
    {
        return $this->hasOne(Tag::class, 'id', 'tag');
    }
}
