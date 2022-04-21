<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\Shell;

class Bin extends Model
{
    use HasFactory;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('in_');
    }

    public function __destruct()
    {
        $this->getConnection()->setTablePrefix('sp_');
    }
    protected $fillable = [
        'shelve_id',
        'name',
        'depth',
        'width',
        'height',
        'zone',
    ];

    public function shelves() {
        return $this->hasOne(Shelve::class,' id','shelve_id');
    } 
   
}
