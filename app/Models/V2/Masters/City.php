<?php

namespace App\Models\V2\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table ='cities';
    protected $fillable = ['state_id','name'];

    
    public function states()
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }

    public function countries()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
