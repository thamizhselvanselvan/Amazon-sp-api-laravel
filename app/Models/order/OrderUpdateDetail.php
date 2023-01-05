<?php

namespace App\Models\order;

use App\Models\order\Order;
use App\Models\Aws_credential;
use Illuminate\Database\Eloquent\Model;
use App\Models\order\OrderSellerCredentials;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderUpdateDetail extends Model
{
    use HasFactory;
    protected $connection = 'order';

    protected $fillable = [
        'store_id',
        'amazon_order_id',
        'order_item_id',
        'courier_name',
        'courier_awb',
        'zoho_id',
        'zoho_order_id',
        'order_status',
    ];

    public function order_seller_cred()
    {
        return $this->hasOne(OrderSellerCredentials::class, 'seller_id', 'store_id');
    }
}
