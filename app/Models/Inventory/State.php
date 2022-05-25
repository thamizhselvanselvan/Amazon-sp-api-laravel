<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
 
    protected $connection = 'inventory';

    public function citys()
    {
        return $this->hasMany(City::class);
    }

    public function countrys()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
