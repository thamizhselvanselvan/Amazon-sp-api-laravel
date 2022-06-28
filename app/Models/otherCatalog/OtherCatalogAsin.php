<?php

namespace App\Models\otherCatalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherCatalogAsin extends Model
{
    use HasFactory;
    protected $fillable = [

        'user_id',
        'asin',
        'status',
        'source'
    ];
}
