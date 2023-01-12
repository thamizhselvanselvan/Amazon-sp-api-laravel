<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;
    protected $table = 'labels';
    protected $fillable = [
        'status',
        'order_no',
        'awb_no',
        'inward_awb',
        'bag_no',
        'forwarder',
    ];

    // public function __construct(array $attributes = [])
    // {
    //     parent::__construct($attributes);
    //     $this->getConnection()->setTablePrefix('');
    // }
}
