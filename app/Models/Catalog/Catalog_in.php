<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog_in extends Model
{
    use HasFactory;

    protected $connection = 'catalog';
    protected $table = 'catalognewins';

    protected $fillable = [
        'asin',
        'attributes',
        'brand',
        'browse_classification',
        'color',
        'created_at',
        'dimensions',
        'height',
        'images',
        'item_classification',
        'item_name',
        'length',
        'manufacturer',
        'marketplace',
        'model_number',
        'package_quantity',
        'part_number',
        'product_types',
        'seller_id',
        'size',
        'source',
        'style',
        'unit',
        'updated_at',
        'website_display_group',
        'weight',
        'weight_unit',
        'width'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->getConnection()->setTablePrefix('');
    }

    public function getAttributesAttribute($value)
    {
        return json_decode($this->attributes['attributes'], true);
    }

    public function getDimensionsAttribute($value)
    {
        return json_decode($this->attributes['dimensions'], true);
    }

    public function getImagesAttribute($value)
    {
        return json_decode($this->attributes['images'], true);
    }

    public function getBrowseClassificationAttribute($value)
    {
        return json_decode($this->attributes['browse_classification'], true);
    }
}
