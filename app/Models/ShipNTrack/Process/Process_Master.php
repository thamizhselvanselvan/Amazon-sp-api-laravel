<?php

namespace App\Models\ShipNTrack\Process;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process_Master extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'process_masters';
    protected $fillable = [
        'source',
        'destination',
        'process_id',
    ];
}
