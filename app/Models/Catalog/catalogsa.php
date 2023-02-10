<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catalogsa extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
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
