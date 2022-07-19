<?php

namespace App\Models\seller;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerAsinDetails extends Model
{
    protected $connection = 'seller';
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'seller_id',
        'asin',
        'is_fulfilment_by_amazon',
        'price',
        'status',
    ];
}
