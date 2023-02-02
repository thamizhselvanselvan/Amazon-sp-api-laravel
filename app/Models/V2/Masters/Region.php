<?php

namespace App\Models\V2\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'currency_id',
        'region',
        'region_code',
        'url',
        'site_url',
        'marketplace_id',
        'status'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
