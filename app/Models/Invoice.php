<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    public $timestamps = false;
    protected $fillable = [

        'invoice_no',
        'invoice_date',
        'mode',
        'channel',
        'shipped_by',
        'awb_no',
        'arn_no',
        'store_name',
        'store_add',
        'bill_to_name',
        'bll_to_add',
        'ship_to_name',
        'ship_to_add',
        'sku',
        'item_description',
        'hsn_code',
        'qty',
        'currency',
        'product_price',
        'taxable_value',
        'total_including_taxes',
        'grand_total',
        'no_of_pcs',
        'packing',
        'dimension',
        'actual_weight',
        'charged_weight',
        'clientcode',
        'bag_no',
        'amazon_order_identifier'

    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('');
    }

    public function __distruct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('sp_');
    }
}
