<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $connection =  'inventory';
    Protected $table = "countries";
    protected $fillable = ['name','country_code','code','numeric_code','phone_code','capital','currency','currency_name','currency_symbol'];

    // public function states()
    // {
    //     return $this->hasMany(State::class ,'id', 'state_id');
    // }
   
}
