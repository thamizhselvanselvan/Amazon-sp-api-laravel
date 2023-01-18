<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mws_region extends Model
{
    use SoftDeletes;

    protected $connection = 'buybox';
    protected $table = 'mws_regions';

    protected $fillable = [
        'region',
        'region_code',
        'url',
        'site_url',
        'marketplace_id',
        'status',
        'currency_id'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function aws_credential()
    {
        return $this->hasMany(Aws_credential::class, 'mws_region_id', 'id')->take(3);
    }

    public function aws_credential1()
    {
        return $this->hasMany(Aws_credential::class, 'mws_region_id', 'id');
    }

    public function aws_verified()
    {
        return $this->aws_credential()->where('verified', 1);
    }

    public function aws_verified1()
    {
        return $this->aws_credential1()->where('verified', 1);
    }
}
