<?php

namespace App\Services\AWS_Business_API\Search_Product_API;

use App\Models\Mws_region;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;
use App\Services\AWS_Business_API\AWS_POC\Search_Product_Request;
use App\Services\Catalog\PriceConversion;
use App\Services\Cliqnshop\CliqnshopCataloginsert;
use App\Services\SP_API\API\NewCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Search_Product
{
    public function SearchProductByKey($searchKey)
    {
        $productSearchApi = new Search_Product_Request();
        $getProducts = $productSearchApi->getASIN($searchKey, 'key');

        
        $siteIds = DB::connection('cliqnshop')->table('mshop_locale_site')->pluck('siteid');
        foreach ($siteIds as $siteId) {
            $source = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $siteId)->pluck('code')->toArray();
            
            $count = 0;
            $count2 = 0;
            $catalogs = [];
            $product_asin = [];
            $productPrice1 = [];
            $productTitle = [];
            $ignore_key_for_cliqnshop = '';
            
            $aws_id = null;
            $seller_id = null;
            $country_code = 'US';
            $ignore_key = [];
            
            if ($source[0] == 'in') {
                $ignore_key = DB::connection('cliqnshop')->table('cns_ban_keywords')->where('site_id', $siteId)->pluck('keyword')->toArray() ? $ignore_key : ['Revolver','Gun','Pistol'] ;
                $price_conversion_method = 'USAToINDB2C';
                $ignore_key_for_cliqnshop = ucwords(str_replace(',', '|', implode(',',$ignore_key)), '|');
            }
            if ($source[0] == 'uae') {
                $ignore_key = DB::connection('cliqnshop')->table('cns_ban_keywords')->where('site_id', $siteId)->pluck('keyword')->toArray() ? $ignore_key : ['Walkie','Talkies','Radio'] ;
                $price_conversion_method = 'USATOUAE';
                $ignore_key_for_cliqnshop = ucwords(str_replace(',', '|', implode(',',$ignore_key)), '|');
            }

            $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->toArray();
            $token = $mws_regions[0]['aws_verified'][1]['auth_code'];
            foreach ($getProducts->products as $key => $getProduct) {

                
                if (preg_match("(" . $ignore_key_for_cliqnshop . ")", $getProduct->title) !== 1 && preg_match("(" . $ignore_key_for_cliqnshop . ")", $getProduct->productDescription) !== 1) {
                    
                    
                    if ($count2 <= 10) {
                        // $productTitle[] = $getProduct->title;
                        $ProductPriceRequest = new ProductsRequest();
                        $productPrice = $ProductPriceRequest->getASINpr($getProduct->asin);
                        $prices = isset($productPrice->includedDataTypes->OFFERS) ? $productPrice->includedDataTypes->OFFERS : '';

                        if (isset($prices[0])) {

                            $product_asin[] = $getProduct->asin;
                            $productPrice1[] = isset($prices[0]->listPrice->value) != '' ? $prices[0]->listPrice->value->amount : $prices[0]->price->value->amount;
                        }
                    }

                    if ($count == 10) {

                        $catalog_for_cliqnshop = new NewCatalog();
                        $catalogs = $catalog_for_cliqnshop->FetchDataFromCatalog($product_asin, $country_code, $seller_id, $token, $aws_id);
                        // $count = 0;

                    }
                    $count++;
                    $count2++;
                }
            }

            $catalog_for_cliqnshop = [];

            foreach ($catalogs as $key1 => $catalog_data) {
                foreach ($catalog_data as $key2 => $catalog) {

                    if ($key2 == 'attributes') {
                        $attributes = json_decode($catalog);
                        if (array_key_exists('bullet_point', (array) $attributes)) {
                            $catalog_for_cliqnshop[$key1]['short_description'] = isset($attributes->bullet_point[0]->value) ? $attributes->bullet_point[0]->value : '';
                            $long_desc = '';
                            foreach ($attributes->bullet_point as $bullet_point) {
                                $long_desc .= "<p>" . (isset($bullet_point->value) ? $bullet_point->value : '');
                                $catalog_for_cliqnshop[$key1]['long_description'] = $long_desc;
                            }
                        }

                        if (array_key_exists('generic_keyword', (array) $attributes)) {
                            $catalog_for_cliqnshop[$key1]['generic_keyword'] = isset($attributes->generic_keyword[0]->value) ? $attributes->generic_keyword[0]->value : '';
                            $gener_key = [];
                            foreach ($attributes->generic_keyword as $generic) {
                                // $gener_key[] = explode(",", $generic->value);
                                $gener_key[] = preg_split("/[,;]/", $generic->value);
                                $catalog_for_cliqnshop[$key1]['generic_keywords'] = $gener_key;
                            }
                        }
                    }

                    if ($key2 == 'browseClassification') {
                        $classifications = json_decode($catalog);
                        if (array_key_exists('classificationId', (array) $classifications)) {
                            $catalog_for_cliqnshop[$key1]['category_code'] = $classifications->classificationId;
                        }
                    }
                    if ($key2 == 'images') {
                        $catalog_images = json_decode($catalog);
                        foreach ($catalog_images[0]->images as $key3 => $images) {
                            if ($key3 <= 10 && $images->height >= 1000 && $images->height <= 2000) {
                                $catalog_for_cliqnshop[$key1]['images'][$catalog_data['asin']]['image' . $key3 + 1] = $images->link;
                            }
                        }
                    }

                    $catalog_for_cliqnshop[$key1]['asin'] = $catalog_data['asin'];
                    $catalog_for_cliqnshop[$key1]['itemName'] = isset($catalog_data['itemName']) ? $catalog_data['itemName'] : '';
                    $catalog_for_cliqnshop[$key1]['brand'] = isset($catalog_data['brand']) ? $catalog_data['brand'] : '';
                    $catalog_for_cliqnshop[$key1]['color'] = isset($catalog_data['color']) ? $catalog_data['color'] : '';
                    $catalog_for_cliqnshop[$key1]['unit'] = isset($catalog_data['unit']) ? $catalog_data['unit'] : '';
                    $catalog_for_cliqnshop[$key1]['length'] = isset($catalog_data['length']) ? round($catalog_data['length'], 2) : '';
                    $catalog_for_cliqnshop[$key1]['width'] = isset($catalog_data['width']) ? round($catalog_data['width'], 2) : '';
                    // $catalog_for_cliqnshop[$key1]['weight']     = isset($catalog_data['weight']) ? $catalog_data['weight'] : '';
                    // $catalog_for_cliqnshop[$key1]['price_US']      = isset($productPrice1[$key1]) ? $productPrice1[$key1] : '';
                    if (isset($catalog_data['weight'])) {

                        $price_convert = new PriceConversion();
                        $catalog_for_cliqnshop[$key1]['price'] = $price_convert->$price_conversion_method($catalog_data['weight'], $productPrice1[$key1]);
                    }
                }
            }
            foreach ($catalog_for_cliqnshop as $cliqnshop_catalog) {

                if (isset($cliqnshop_catalog['price']) && isset($cliqnshop_catalog['images'])) {
                    $category = $cliqnshop_catalog['category_code'] ?? 'demo-new';
                    $asin = $cliqnshop_catalog['asin'];
                    $item_name = $cliqnshop_catalog['itemName'];
                    $brand = $cliqnshop_catalog['brand'];
                    $brand_label = $cliqnshop_catalog['brand'];
                    $color_key = $cliqnshop_catalog['color'];
                    $label = $cliqnshop_catalog['color'];
                    $length_unit = $cliqnshop_catalog['unit'];
                    $length_value = $cliqnshop_catalog['length'];
                    $width_unit = $cliqnshop_catalog['unit'];
                    $width_value = $cliqnshop_catalog['width'];
                    $Price_US_IN = $cliqnshop_catalog['price'];
                    $image_array = $cliqnshop_catalog['images'];
                    $short_description = $cliqnshop_catalog['short_description'] ?? '';
                    $long_description = $cliqnshop_catalog['long_description'] ?? '';
                    $generic_keywords = $cliqnshop_catalog['generic_keywords'] ?? '';

                    $cliqnshop = new CliqnshopCataloginsert();
                    $cliqnshop->insertdata_cliqnshop($siteId, $category, $asin, $item_name, $brand, $brand_label, $color_key, $label, $length_unit, $length_value, $width_unit, $width_value, $Price_US_IN, $image_array, $searchKey, $short_description, $long_description, $generic_keywords);
                }
            }

        }
    }
}
