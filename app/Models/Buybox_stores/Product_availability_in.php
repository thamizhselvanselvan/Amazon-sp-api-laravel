<?php

namespace App\Models\Buybox_stores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_availability_in extends Model
{
    use HasFactory;

    protected $connection = 'buybox_stores';
    protected $table = 'product_availability_ins';
    
    protected $fillable = [
        "store_id",
        "asin",
        "product_sku",
        "current_availability",
        "push_availability",
        "push_availability_reason",
        "feedback_id",
        "feedback_response",
        "feedback_status",
        "push_status",
        "export_status"
    ];
}
