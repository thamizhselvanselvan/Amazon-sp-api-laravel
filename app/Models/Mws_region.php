<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mws_region extends Model
{
     use SoftDeletes;

    protected $connection = 'mysql1';

    protected $fillable = [
        'region',
        'region_code',
        'url',
        'site_url',
        'marketplace_id',
        'status',
        'currency_id'
    ];

    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

}
