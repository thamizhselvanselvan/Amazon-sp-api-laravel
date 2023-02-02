<?php

namespace App\Models\V2\OMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'oms';
    protected $fillable = [
        'code',
        'status',
        'active',
    ];
}