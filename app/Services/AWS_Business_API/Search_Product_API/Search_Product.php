<?php

namespace App\Services\AWS_Business_API\Search_Product_API;

use App\Models\Mws_region;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\NewCatalog;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;
use App\Services\AWS_Business_API\AWS_POC\Search_Product_Request;

class Search_Product
{
    public function SearchProductByKey($key)
    {

        $productSearchApi = new Search_Product_Request();
        $getProducts = $productSearchApi->getASIN($key);
        $product_asin = [];
        $productPrice1 = [];
        $seller_id = NULL;
        $aws_id = NULL;
        $country_code = 'US';
        $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->toArray();
        $token = $mws_regions[0]['aws_verified'][0]['auth_code'];
        $count = 0;
        $count2 = 0;
        $catalogs = [];

        foreach ($getProducts->products as $key => $getProduct) {

            if ($count2 <= 9) {

                $product_asin[] = $getProduct->asin;
                $ProductPriceRequest = new ProductsRequest();
                $productPrice = $ProductPriceRequest->getASINpr($getProduct->asin);
                $prices = $productPrice->includedDataTypes->OFFERS;
                $productPrice1[] = $prices[0]->listPrice->value != '' ? $prices[0]->listPrice->value->amount : $prices[0]->price->value->amount;
            }

            if ($count == 9) {
                $catalog_for_cliqnshop = new NewCatalog();
                $catalogs = $catalog_for_cliqnshop->FetchDataFromCatalog($product_asin, $country_code, $seller_id, $token, $aws_id);
                $count = 0;
            }

            $count++;
            $count2++;
        }
        $catalog_data = [];
        foreach ($catalogs as $key1 => $catalog) {
            foreach ($catalog as $key2 => $cat_data) {
                if ($key2 == 'attributes') {
                    $attributes = json_decode($cat_data);
                    $catalog_data[$key1]['short_description'] = $attributes->bullet_point[0]->value;
                    $attr_data = '';
                    foreach ($attributes->bullet_point as $bullet_point) {
                        $attr_data .=  $bullet_point->value . ' ';
                        $catalog_data[$key1]['long_description'] = $attr_data;
                    }
                }
                if ($key2 == 'browseClassification') {
                    $classifications = json_decode($cat_data);

                    $catalog_data[$key1]['category_code'] = $classifications->classificationId;
                }
                if ($key2 != 'seller_id' && $key2 != 'browseClassification' && $key2 != 'images' && $key2 != 'attributes' && $key2 != 'dimensions' && $key2 != 'source' && $key2 != 'productTypes' && $key2 != 'marketplace' && $key2 != 'itemClassification' && $key2 != 'packageQuantity' && $key2 != 'size' && $key2 != 'size' && $key2 != 'websiteDisplayGroup' && $key2 != 'modelNumber' && $key2 != 'partNumber' && $key2 != 'manufacturer') {

                    $catalog_data[$key1][$key2] = $cat_data;
                    $catalog_data[$key1]['price'] = $productPrice1[$key1];
                }
                if ($key2 == 'images') {
                    $images = json_decode($cat_data);
                    // $catalog_data[] = $images[0]->images;
                    foreach ($images[0]->images as $key4 => $image) {
                        if ($key4 <= 10 && $image->height >= 75) {
                            $catalog_data[$key1]['image' . $key4] = $image->link;
                        }
                    }
                }
            }
        }
        return $catalog_data;
    }
}
