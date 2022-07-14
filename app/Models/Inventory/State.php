<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
 
    protected $connection = 'inventory';
    protected $table = 'states';
    protected $fillable = ['country_id','name','created_at','updated_at'];

    public function citys()
    {
        return $this->hasMany(City::class);
    }

    public function countrys()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
