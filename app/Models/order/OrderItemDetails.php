<?php

namespace App\Models\order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemDetails extends Model
{
    use HasFactory;
    protected $connection = 'order';
    protected $table = 'orderitemdetails';
}
