<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $fillable = ['name'];

    
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
