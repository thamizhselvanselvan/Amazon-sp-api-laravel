<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aws_credential extends Model
{
    use HasFactory;

    protected $connection = 'buybox';

    protected $fillable = [
        'seller_id',
        'mws_region_id',
        'store_name',
        'merchant_id',
        'auth_code',
        'verified',
        'country_priority',
        'horizon_priority',
        'credential_priority	'
    ];

    public function mws_region()
    {
        return $this->hasOne(Mws_region::class, 'id', 'mws_region_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function seller()
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    public function product_count()
    {
        return $this->belongsTo(Product::class, 'seller_id', 'seller_id');
    }
}
