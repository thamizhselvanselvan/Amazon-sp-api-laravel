<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorReporting extends Model
{
    use HasFactory;
    protected $fillable = [
        'queue_type',
        'identifier',
        'identifier_type',
        'source',
        'aws_key',
        'error_code',
        'message',
    ];
}
