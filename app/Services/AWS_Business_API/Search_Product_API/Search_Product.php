<?php

namespace App\Services\AWS_Business_API\Search_Product_API;

use App\Models\Mws_region;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\NewCatalog;
use App\Services\Catalog\PriceConversion;
use App\Services\Cliqnshop\CliqnshopCataloginsert;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;
use App\Services\AWS_Business_API\AWS_POC\Search_Product_Request;
use Exception;

use function GuzzleHttp\Promise\exception_for;

class Search_Product
{
    public function SearchProductByKey($key)
    {

        $productSearchApi = new Search_Product_Request();
        $getProducts = $productSearchApi->getASIN($key);
        $count = 0;
        $count2 = 0;
        $catalogs = [];
        $product_asin = [];
        $productPrice1 = [];

        $aws_id = NULL;
        $seller_id = NULL;
        $country_code = 'US';
        if ($country_code == 'US') {
            $price_conversion_method = 'USAToINDB2C';
        } else {
            $price_conversion_method = 'USATOUAE';
        }
        $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->toArray();
        $token = $mws_regions[0]['aws_verified'][0]['auth_code'];

        foreach ($getProducts->products as $key => $getProduct) {

            if ($count2 <= 9) {

                $product_asin[] = $getProduct->asin;
                $ProductPriceRequest = new ProductsRequest();
                $productPrice = $ProductPriceRequest->getASINpr($getProduct->asin);
                $prices = isset($productPrice->includedDataTypes->OFFERS) ? $productPrice->includedDataTypes->OFFERS : '';
                if (isset($prices[0])) {

                    $productPrice1[] = isset($prices[0]->listPrice->value) != '' ? $prices[0]->listPrice->value->amount : $prices[0]->price->value->amount;
                } else {
                    $productPrice1[] = '';
                }
            }

            if ($count == 9) {
                $catalog_for_cliqnshop = new NewCatalog();
                $catalogs = $catalog_for_cliqnshop->FetchDataFromCatalog($product_asin, $country_code, $seller_id, $token, $aws_id);
                $count = 0;
            }

            $count++;
            $count2++;
        }

        // return $prices;
        // return $data = [
        //     'asin' => $product_asin,
        //     'price' => $productPrice1
        // ];
        $catalog_for_cliqnshop = [];
        foreach ($catalogs as $key1 => $catalog_data) {
            foreach ($catalog_data as $key2 => $catalog) {
                if ($key2 == 'attributes') {
                    $attributes = json_decode($catalog);
                    if (array_key_exists('bullet_point', (array)$attributes)) {
                        $catalog_for_cliqnshop[$key1]['short_description'] = isset($attributes->bullet_point[0]->value) ? $attributes->bullet_point[0]->value : '';
                        $long_desc = '';
                        foreach ($attributes->bullet_point as $bullet_point) {
                            $long_desc .= "<p>" . (isset($bullet_point->value) ? $bullet_point->value : '');
                            $catalog_for_cliqnshop[$key1]['long_description'] = $long_desc;
                        }
                    }
                }
                if ($key2 == 'browseClassification') {
                    $classifications = json_decode($catalog);
                    if (array_key_exists('classificationId', (array)$classifications)) {
                        $catalog_for_cliqnshop[$key1]['category_code'] = $classifications->classificationId;
                    }
                }
                if ($key2 == 'images') {
                    $catalog_images = json_decode($catalog);
                    foreach ($catalog_images[0]->images as $key3 => $images) {
                        if ($key3 <= 9 && $images->height >= 76) {
                            $catalog_for_cliqnshop[$key1]['images'][$catalog_data['asin']]['image' . $key3 + 1] = $images->link;
                        }
                    }
                }
                $catalog_for_cliqnshop[$key1]['asin']       = $catalog_data['asin'];
                $catalog_for_cliqnshop[$key1]['itemName']   = isset($catalog_data['itemName']) ? $catalog_data['itemName'] : '';
                $catalog_for_cliqnshop[$key1]['brand']      = isset($catalog_data['brand']) ? $catalog_data['brand'] : '';
                $catalog_for_cliqnshop[$key1]['color']      = isset($catalog_data['color']) ? $catalog_data['color'] : '';
                $catalog_for_cliqnshop[$key1]['unit']       = isset($catalog_data['unit']) ? $catalog_data['unit'] : '';
                $catalog_for_cliqnshop[$key1]['length']     = isset($catalog_data['length']) ? $catalog_data['length'] : '';
                $catalog_for_cliqnshop[$key1]['width']      = isset($catalog_data['width']) ? $catalog_data['width'] : '';
                $catalog_for_cliqnshop[$key1]['weight']      = isset($catalog_data['weight']) ? $catalog_data['weight'] : '';
                // $catalog_for_cliqnshop[$key1]['price_US']      = isset($productPrice1[$key1]) ? $productPrice1[$key1] : '';
                if (isset($catalog_data['weight']) && isset($productPrice1[$key1]) > 0) {

                    $price_convert = new PriceConversion();
                    $catalog_for_cliqnshop[$key1]['price'] = $price_convert->$price_conversion_method($catalog_data['weight'], $productPrice1[$key1]);
                }
            }
        }
        // return $catalog_for_cliqnshop;
        foreach ($catalog_for_cliqnshop as $cliqnshop_catalog) {

            $asin               = $cliqnshop_catalog['asin'];
            $item_name          = $cliqnshop_catalog['itemName'];
            $brand              = $cliqnshop_catalog['brand'];
            $brand_label        = $cliqnshop_catalog['brand'];
            $color_key          = $cliqnshop_catalog['color'];
            $label              = $cliqnshop_catalog['color'];
            $length_unit        = $cliqnshop_catalog['unit'];
            $length_value       = $cliqnshop_catalog['length'];
            $width_unit         = $cliqnshop_catalog['unit'];
            $width_value        = $cliqnshop_catalog['width'];
            $Price_US_IN        = $cliqnshop_catalog['price'] ??  '';
            $image_array        = $cliqnshop_catalog['images'];
            $short_description  = $cliqnshop_catalog['short_description'] ?? '';
            $long_description   = $cliqnshop_catalog['long_description'] ?? '';


            $cliqnshop = new CliqnshopCataloginsert();
            $cliqnshop->insertdata_cliqnshop($asin,  $item_name,  $brand,  $brand_label,  $color_key,  $label,  $length_unit,  $length_value,  $width_unit,  $width_value,  $Price_US_IN,  $image_array,  $short_description,  $long_description);
        }


        // $catalog_data = [];
        // foreach ($catalogs as $key1 => $catalog) {
        //     foreach ($catalog as $key2 => $cat_data) {
        //         if ($key2 == 'attributes') {
        //             $attributes = json_decode($cat_data);
        //             $catalog_data[$key1]['short_description'] = $attributes->bullet_point[0]->value;
        //             $attr_data = '';
        //             foreach ($attributes->bullet_point as $bullet_point) {
        //                 $attr_data .=  $bullet_point->value . ' ';
        //                 $catalog_data[$key1]['long_description'] = $attr_data;
        //             }
        //         }
        //         if ($key2 == 'browseClassification') {
        //             $classifications = json_decode($cat_data);

        //             $catalog_data[$key1]['category_code'] = $classifications->classificationId;
        //         }
        //         if ($key2 != 'seller_id' && $key2 != 'browseClassification' && $key2 != 'images' && $key2 != 'attributes' && $key2 != 'dimensions' && $key2 != 'source' && $key2 != 'productTypes' && $key2 != 'marketplace' && $key2 != 'itemClassification' && $key2 != 'packageQuantity' && $key2 != 'size' && $key2 != 'size' && $key2 != 'websiteDisplayGroup' && $key2 != 'modelNumber' && $key2 != 'partNumber' && $key2 != 'manufacturer') {

        //             $catalog_data[$key1][$key2] = $cat_data;
        //             $catalog_data[$key1]['price'] = $productPrice1[$key1];
        //         }
        //         // if ($key2 == 'images') {
        //         //     $images = json_decode($cat_data);
        //         //     foreach ($images[0]->images as $key4 => $image) {
        //         //         if ($key4 <= 9 && $image->height >= 75) {
        //         //             $catalog_data[$key1]['image' . $key4 + 1] = $image->link;
        //         //         }
        //         //     }
        //         // }
        //         if ($key2 == 'images') {
        //             $images = json_decode($cat_data);
        //             foreach ($images[0]->images as $key4 => $image) {
        //                 if ($key4 <= 9 && $image->height != 75) {
        //                     $catalog_data[$key1]['images'][$catalog['asin']]['image' . $key4 + 1] = $image->link;
        //                 }
        //             }
        //         }
        //     }
        // }
        // $image_array = [];

        // foreach ($catalog_data as $key5 => $data) {
        //     $asin               = $data['asin'];
        //     $item_name          = $data['itemName'];
        //     $brand              = isset($data['brand']) ? $data['brand'] : '';
        //     $brand_label        = isset($data['brand']) ? $data['brand'] : '';
        //     $color_key          = isset($data['color']) ? $data['color'] : '';
        //     $label              = isset($data['color']) ? $data['color'] : '';
        //     $length_unit        = $data['unit'];
        //     $length_value       = $data['length'];
        //     $width_unit         = $data['unit'];
        //     $width_value        = $data['width'];
        //     $Price_US_IN        = $data['price'];
        //     $image_array            = $data['images'];
        //     $short_description  = $data['short_description'];
        //     $long_description   = $data['long_description'];
        //     $cliqnshop = new CliqnshopCataloginsert();
        //     $cliqnshop->insertdata_cliqnshop($asin,  $item_name,  $brand,  $brand_label,  $color_key,  $label,  $length_unit,  $length_value,  $width_unit,  $width_value,  $Price_US_IN,  $image_array,  $short_description,  $long_description);
        // }
        // return $catalog_data;
    }
}
