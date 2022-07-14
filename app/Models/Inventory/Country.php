<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $connection =  'inventory';
    Protected $table = "countries";
    protected $fillable = ['name','created_at','updated_at'];
}
