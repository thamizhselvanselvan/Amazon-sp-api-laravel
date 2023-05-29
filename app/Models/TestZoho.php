<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestZoho extends Model
{
    protected $connection = 'order';
    protected $table = 'zoho_api_test';
    use HasFactory;
    protected $fillable = [
        'opertaion_type',
        'api_called_through',
        'time'
    ];
}
