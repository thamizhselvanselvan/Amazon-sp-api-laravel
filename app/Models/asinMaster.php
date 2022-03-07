<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asinMaster extends Model
{
    use HasFactory;

    protected $fillable =[

        'asin',
        'source',
        'destination_1',
        'destination_2',
        'destination_3',
        'destination_4',
        'destination_5',
        
    ];

    public function mws_region() {
        return $this->hasOne(Mws_region::class, 'region_code', 'destination_1');

    }

    public function aws_credential() {
        return $this->belongsTo(aws_credentials::class,);
    }

    public function aws(){

    
        return $this->hasOneThrough(
            aws_credentials::class,
            Mws_region::class,
            'region_code', // Foreign key on orders table...
            'mws_region_id', // Foreign key on products table...
            'source', // Local key on suppliers table...
           // 'id' // Local key on products table...
        );
    }

    public function __distruct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('sa_');
    } 

}
