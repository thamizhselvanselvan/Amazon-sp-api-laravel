<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelves extends Model
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
   
    protected $fillable = ['Shelves_name','No_of_Bins'];
   
}
