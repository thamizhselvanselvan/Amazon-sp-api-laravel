<?php

namespace App\Models\MongoDB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewZoho extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'new_zoho';
}
