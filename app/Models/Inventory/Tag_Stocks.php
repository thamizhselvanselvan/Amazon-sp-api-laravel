<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag_Stocks extends Model
{
    protected $fillable = ['date', 'tag','opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount'];
}
