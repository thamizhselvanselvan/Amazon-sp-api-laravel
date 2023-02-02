<?php

namespace App\Models\V2\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = "countries";
    protected $fillable = ['name', 'country_code', 'code', 'numeric_code', 'phone_code', 'capital', 'currency', 'currency_name', 'currency_symbol'];

    // public function states()
    // {
    //     return $this->hasMany(State::class ,'id', 'state_id');
    // }

}
