<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asinMaster extends Model
{
    use HasFactory;

    protected $fillable =[

        'asin',
        'source',
        'destination_1',
        'destination_2',
        'destination_3',
        'destination_4',
        'destination_5',
        
    ];
}
