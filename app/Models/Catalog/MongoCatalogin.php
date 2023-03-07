<?php

namespace App\Models\Catalog;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MongoCatalogin extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'catalogins';
}
