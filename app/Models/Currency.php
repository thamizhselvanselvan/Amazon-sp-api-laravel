<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql1';

    protected $fillable = [
        'name', 'code', 'status'
    ];

    public $timestamps = false;

}
