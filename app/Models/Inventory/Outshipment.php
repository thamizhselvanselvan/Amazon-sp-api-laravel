<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outshipment extends Model
{
    use HasFactory;

    protected $connection = 'inventory';
    protected $fillable = ['destination_id','asin','ship_id','item_name','quantity','price'];

    public function vendors() {
        return $this->hasOne(Vendor::class, 'id', 'destination_id');
    }
}
