<?php

namespace App\Models\seller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsinMasterSeller extends Model
{
    protected $connection = 'seller';
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'asin',
        'seller_id',
        'source',
        'destination_1',
        'destination_2',
        'destination_3',
        'destination_4',
        'destination_5',
    ];
}
