<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratemaster extends Model
{
    use HasFactory;

    protected $table = 'rate_masters';

    protected $fillable = [
        'weight',
        'base_rate',
        'commission',
        'source_destination'
    ];
}
