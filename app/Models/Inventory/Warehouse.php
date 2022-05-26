<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    use HasFactory;
    protected $connection = 'inventory';

    protected $fillable = [
        'name',
        'address_1',
        'address_2',
        'city',
        'state',
        'country',
        'pin_code',
        'contact_person_name',
        'phone_number',
        'email',
    ];

    public function racks()
    {
        return $this->hasMany(Racks::class);
    }
    public function countrys()
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }
   
    public function states()
    {
        return $this->hasOne(State::class, 'id', 'country');
    }

    public function citys()
    {
        return $this->hasOne(City::class ,'id','state');
    }
}
