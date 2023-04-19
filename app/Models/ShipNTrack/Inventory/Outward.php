<?php

namespace App\Models\ShipNtrack\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outward extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'outwardings';
    protected $fillable = [
        'manifest_id',
        'mode',
        'type',
        'awb_number',
        'status'
    ];
}
