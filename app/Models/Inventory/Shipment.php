<?php

namespace App\Models\inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;
    protected $connection = 'in';
    protected $fillable = ['source_id','asin','ship_id','item_name','quantity','price'];

    public function sources() {
        return $this->hasOne(Source::class, 'id', 'source_id');
    }
}
