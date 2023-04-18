<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class zoho extends Model
{
    use HasFactory;
    protected $connection = "mongodb";
    protected $collection = "robin";
}
