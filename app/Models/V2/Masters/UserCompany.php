<?php

namespace App\Models\V2\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserCompany extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_companys';
    protected $fillable = ['user_id', 'company_id'];
}
