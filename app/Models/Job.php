<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    
    protected $table= 'jobs';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('sa_');
    } 

    protected $fillable = [
        	'queue',
            'payload',
            'attempts',
            'reserved_at',
            'available_at',
            'created_at'	

    ];
}
