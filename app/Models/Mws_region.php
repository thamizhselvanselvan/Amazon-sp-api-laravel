<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mws_region extends Model
{
    use SoftDeletes;

    protected $table = 'mws_regions';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->getConnection()->setTablePrefix('bb_');
    }

    public function __destruct()
    {
        $this->getConnection()->setTablePrefix('sa_');
    }

    protected $fillable = [
        'region',
        'region_code',
        'url',
        'site_url',
        'marketplace_id',
        'status',
        'currency_id'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function aws_credential()
    {
        return $this->belongsTo(Aws_credentials::class, 'mws_region_id', 'id');
    }
}
