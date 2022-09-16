<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogMissingAsin extends Model
{
    use HasFactory;
    protected $connection = 'catalog';
    protected $fillable = [
        'asin',
        'user_id',
        'source',
        'status',
    ];
}
