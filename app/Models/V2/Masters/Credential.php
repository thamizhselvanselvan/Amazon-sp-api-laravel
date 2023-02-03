<?php

namespace App\Models\V2\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credential extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'company_id',
        'store_name','merchant_id',
        'authcode','region_id'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    
    public function company()
    {
        return $this->hasOne(CompanyMaster::class,'id', 'company_id');
    }
}
