<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmazonInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'awb',
        'amazon_order_id',
        'booking_date',
        'status',
    ];
}
