<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OthercatDetailsIndia extends Model
{
    use HasFactory;
    protected $connection = 'aws';
    protected $table = 'othercat_details_india';
}
