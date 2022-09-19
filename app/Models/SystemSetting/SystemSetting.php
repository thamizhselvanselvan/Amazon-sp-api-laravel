<?php

namespace App\Models\SystemSetting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemSetting extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'key',
        'value',
        'status',
    ];
}
