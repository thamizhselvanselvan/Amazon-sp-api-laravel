<?php

namespace App\Models\MongoDBBusiness;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Product_Details extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'product_details';

    protected $fillable = [
        'asin',
        'asin_type',
        'signedProductId ',
        'offers',
        'availability',
        'buyingGuidance',
        'fulfillmentType',
        'merchant',
        'offerId',
        'price',
        'listPrice',
        'productCondition',
        'condition',
        'quantityLimits',
        'deliveryInformation',
        'features',
        'taxonomies',
        'title',
        'url',
        'productOverview',
        'productVariations',
    ];
}
