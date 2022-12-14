<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $connection = 'catalog';
    protected $guarded = [];

    public function getUpdatedAtAttibutes()
    {
        return isset($this->attributes['updated_at']) ? date("d-m-Y h:i:s", strtotime($this->attributes['updated_at'])) : 'NA';
        return isset($record['updated_at']) ? date("d-m-Y h:i:s", strtotime($record['updated_at'])) : 'NA';
    }
}
