<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;
    protected $table = 'backup';
    // protected $table = 'product_lowest_priced_offers';

    protected $fillable = [
        'connection',
        'table_name',
        'status'
    ];
}
