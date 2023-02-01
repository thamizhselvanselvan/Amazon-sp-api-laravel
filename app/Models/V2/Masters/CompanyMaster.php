<?php

namespace App\Models\V2\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'id','user_id'
    ];
}
