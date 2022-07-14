<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispose extends Model
{
    use HasFactory;
    protected $connection = 'inventory';
    protected $fillable = ['reason'];
}
