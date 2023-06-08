<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZohoMissing extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $table = 'zoho_missing';

    protected $fillable = [
        'country_code',
        'title',
        'asin',
        'amazon_order_id',
        'order_item_id',
        'status',
        'price',
        'missing_details'
    ];

    public function getMissingDetailsAttribute($value)
    {
        return json_decode($this->attributes['missing_details']);
    }

    public function setMissingDetailsAttribute($value)
    {
        return json_encode($this->attributes['missing_details']);
    }
}
