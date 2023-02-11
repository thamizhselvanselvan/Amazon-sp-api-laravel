<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogus extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
    protected $table = 'cataloguss';
    protected $fillable = [
        'asin',
        'source',
        'length',
        'width',
        'height',
        'unit',
        'weight',
        'weight_unit',
        'classification_id',
        'brand',
        'manufacturer'
    ];
}
