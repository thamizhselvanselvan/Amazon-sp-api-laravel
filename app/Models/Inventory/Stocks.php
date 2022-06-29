<?php

namespace App\Models\inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    protected $connection = 'inventory';
    protected $table = 'stocks';
    protected $fillable = ['date','opeaning_stock','opeaning_amount','inwarding','inw_amount','outwarding','outw_amount','closing_stock','closing_amount'];

}
