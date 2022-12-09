<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog_sa extends Model
{
    use HasFactory;

    protected $connection = 'catalog';
    protected $table = 'catalognewsas';

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

    public function setAttributesAttribute($value)
    {
        return json_encode($this->attributes['attributes']);
    }

    public function setDimensionsAttribute($value)
    {
        return json_encode($this->attributes['dimensions']);
    }

    public function setProductTypesAttribute($value)
    {
        return json_encode($this->attributes['product_types']);
    }

    public function setImagesAttribute($value)
    {
        return json_encode($this->attributes['images']);
    }

    public function setBrowseClassificationAttribute($value)
    {
        return json_encode($this->attributes['browse_classification']);
    }

    public function getAttributesAttribute($value)
    {
        return json_decode($this->attributes['attributes'], true);
    }

    public function getDimensionsAttribute($value)
    {
        return json_decode($this->attributes['dimensions'], true);
    }

    public function getProductTypesAttribute($value)
    {
        return json_decode($this->attributes['product_types'], true);
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
