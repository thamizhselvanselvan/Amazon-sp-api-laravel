<?php

namespace App\Models\V2\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'currencies';
    
    protected $fillable = [
        'name',
        'code','status'
    ];
}
