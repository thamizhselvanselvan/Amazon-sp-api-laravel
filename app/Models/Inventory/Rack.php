<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('in_');
    }

    public function __destruct()
    {
        $this->getConnection()->setTablePrefix('sp_');
    }
    use HasFactory;
    protected $fillable = ['name'];   
}
