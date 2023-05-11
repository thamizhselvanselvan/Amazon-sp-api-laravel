<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewZoho extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'new_zoho';
}
