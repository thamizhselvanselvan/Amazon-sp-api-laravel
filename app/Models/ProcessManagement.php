<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessManagement extends Model
{
    use HasFactory;
    protected $connection = 'web';
    protected $table = 'process_managements';
    protected $fillable = [
        'module',
        'description',
        'command_name',
        'command_start_time',
        'command_end_time',
        'status',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('sp_');
    }

    public function __distruct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('sp_');
    }
}
