<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asin_destionation extends Model
{
    use HasFactory, SoftDeletes;
    protected $connection = 'catalog';

    protected $fillable = [
        'asin',
        'user_id',
        'source',
        'priority',
    ];
}
