<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratemaster extends Model
{
    use HasFactory;

    protected $connection = 'shipntracking';
    protected $table = 'ratemasters';
}
