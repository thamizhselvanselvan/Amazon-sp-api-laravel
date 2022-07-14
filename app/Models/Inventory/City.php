<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $table ='cities';
    protected $fillable = ['state_id','name','created_at','updated_at'];

    
    public function states()
    {
        return $this->hasOne(State::class.'id', 'state_id');
    }

    public function countrys()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
