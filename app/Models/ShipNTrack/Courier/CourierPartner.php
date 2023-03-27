<?php

namespace App\Models\ShipNTrack\Courier;

use Illuminate\Database\Eloquent\Model;
use App\Models\ShipNTrack\Courier\Courier;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierPartner extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'partners';
    protected $fillable = [
        'user_name',
        'courier_id',
        'source',
        'destination',
        'active',
        'type',
        'time_zone',
        'key1',
        'key2',
        'user_id',
        'password',
        'account_id'
    ];
       public function courier_names()
    {
        return $this->hasOne(Courier::class, 'id', 'courier_id');
    }
}
