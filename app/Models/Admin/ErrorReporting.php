<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorReporting extends Model
{
    use HasFactory;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('sp_');
    }
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
