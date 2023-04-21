<?php

namespace App\Models\ShipNTrack\Operation\LabelMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabelMaster extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $fillable = [
        'source',
        'destination',
        'file_path',
        'return_address'
    ];
}
