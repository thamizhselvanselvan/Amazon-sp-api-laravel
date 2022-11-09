<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileManagement extends Model
{
    use HasFactory;

    protected $table = "file_managements";
    protected $fillable = [
        'user_id',
        'type',
        'module',
        'source_destination',
        'file_name',
        'file_path',
        'command_name',
        'command_start_time',
        'command_end_time',
        'status',
        'info',
    ];
}
