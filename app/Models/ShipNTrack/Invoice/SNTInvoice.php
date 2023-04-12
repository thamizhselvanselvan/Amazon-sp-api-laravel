<?php

namespace App\Models\ShipNTrack\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SNTInvoice extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'invoices';
    protected $fillable = [
        'invoice_no',
        'mode',
        'invoice_date',
        'sku',
        'channel',
        'shipped_by',
        'awb_no',
        'arn_no',
        'store_name',
        'store_add',
        'bill_to_name',
        'bill_to_add',
        'ship_to_name',
        'ship_to_add',
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
        'sr_no',
        'clientcode',
    ];
}
