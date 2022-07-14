<?php

namespace App\Models\Inventory;


use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $fillable = ['name','type','country','state','city','currency_id'];


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
    
    public function currencies()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }
}
