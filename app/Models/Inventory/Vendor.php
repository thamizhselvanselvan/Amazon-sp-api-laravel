<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $fillable = ['name','type','country','state','city','currency'];


    public function countrys()
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }
   
    public function states()
    {
        return $this->hasOne(State::class, 'id', 'state');
    }

    public function citys()
    {
        return $this->hasOne(City::class , 'id', 'city');
    }
}
