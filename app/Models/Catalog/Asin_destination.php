<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asin_destination extends Model
{
    use HasFactory;
    protected $connection = 'catalog';

    protected $fillable = [
        'asin',
        'user_id',
        'source',
        'priority',
    ];
}
