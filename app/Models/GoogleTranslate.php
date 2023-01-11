<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleTranslate extends Model
{
    use HasFactory;
    protected $fillable = [
        'amazon_order_identifier',
        'name',
        'address',
        'city',
        'county'
    ];
}
