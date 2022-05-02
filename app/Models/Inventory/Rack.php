<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    use HasFactory;

    protected $connection = 'in';

    // public function __construct(array $attributes = [])
    // {
    //     parent::__construct($attributes);
    //     $this->getConnection()->setTablePrefix('in_');
    // }

    // public function __destruct()
    // {
    //     $this->getConnection()->setTablePrefix('sp_');
    // }

    protected $fillable = ['name','rack_id','warehouse_id'];


    public function shelves()
    {
        return $this->hasMany(Shelve::class);
    }
    
    public function warehouse() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}
