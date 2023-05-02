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
    public function SearchProductByKey($searchKey,$site)
    {
        $productSearchApi = new Search_Product_Request();
        $getProducts = $productSearchApi->getASIN($searchKey, 'key');


        $siteIds = DB::connection('cliqnshop')->table('mshop_locale_site')->pluck('siteid');
        foreach ($siteIds as $siteId) {
            $source = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $siteId)->pluck('code')->toArray();
        if ($source[0] == $site)
        {
            $display_code = 1;    
        }
        else {
            $display_code = 0;
        }
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
                $ignore_key = DB::connection('cliqnshop')->table('cns_ban_keywords')->where('site_id', $siteId)->pluck('keyword')->toArray();
                if ($ignore_key == [])
                {
                    $ignore_key = ['Gun','Revolver','Pistol'];
                }
                $price_conversion_method = 'USAToINDB2C';
                $ignore_key_for_cliqnshop = ucwords(str_replace(',', '|', implode(',',$ignore_key)), '|');
            }
           

            if ($source[0] == 'uae') {
                $ignore_key = DB::connection('cliqnshop')->table('cns_ban_keywords')->where('site_id', $siteId)->pluck('keyword')->toArray();
                if ($ignore_key == [])
                {
                    $ignore_key = ['Radio','Walkie','Talkies'];
                }
                $price_conversion_method = 'USATOUAE';
                $ignore_key_for_cliqnshop = ucwords(str_replace(',', '|', implode(',',$ignore_key)), '|');
            }

            $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->toArray();
            $token = $mws_regions[0]['aws_verified'][0]['auth_code'];
            foreach ($getProducts->products as $key => $getProduct) {


                if (preg_match("(" . $ignore_key_for_cliqnshop . ")", $getProduct->title) !== 1 && preg_match("(" . $ignore_key_for_cliqnshop . ")", $getProduct->productDescription) !== 1) {


                   
                        // $productTitle[] = $getProduct->title;
                        $ProductPriceRequest = new ProductsRequest();
                        $productPrice = $ProductPriceRequest->getASINpr($getProduct->asin);
                        $prices = isset($productPrice->includedDataTypes->OFFERS) ? $productPrice->includedDataTypes->OFFERS : '';

                        if (isset($prices[0])) {

                            $product_asin[] = $getProduct->asin;
                            $productPrice1[] = isset($prices[0]->listPrice->value) != '' ? $prices[0]->listPrice->value->amount : $prices[0]->price->value->amount;
                        }
                    
                    }
                }
                   
                $array_asins = (array_chunk($product_asin, 10));
                $array_prices = (array_chunk($productPrice1, 10));
                foreach ($array_asins as $key => $asin) {
                    foreach($array_prices as $key => $price)
                    {
                        if (!is_array($asin))
                        {
                            $asin = array($asin);
                        }
                        $catalog_for_cliqnshop = new NewCatalog();
                        $catalogs = $catalog_for_cliqnshop->FetchDataFromCatalog($asin, $country_code, $seller_id, $token, $aws_id);
                        // $count = 0;

                   
               

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
                        if (array_key_exists('item_package_dimensions', (array) $attributes)) {
                            $package_dimensions = json_decode($catalog);
                            $catalog_for_cliqnshop[$key1]['item_package_dimensions'] = isset($package_dimensions->item_package_dimensions) ? $package_dimensions->item_package_dimensions: '';
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

                            
                            if (isset($catalog_images[0]->images)) {
                                if ($key3 <= 9 && $images->height > 500 || $images->width > 500) {
                                    $catalog_for_cliqnshop[$key1]['images'][$catalog_data['asin']]['image' . $key3 + 1] = $images->link;
        
                                }
                            // if (isset($catalog_images[0]->images)) {
                            //     foreach ($catalog_images[0]->images as  $counter => $image_data_new) {
                            //         $counter++;

                            //         if (isset($image_data_new->link)) {

                            //             $img1["Images${counter}"] = '';
                            //             if ($counter == 1) {
                            //                 ($img1["Images${counter}"] = $image_data_new->link);
                            //             } else if ($counter == 4) {
                            //                 ($img1["Images${counter}"] = $image_data_new->link);
                            //             } else if ($counter == 7) {
                            //                 ($img1["Images${counter}"] = $image_data_new->link);
                            //             } else if ($counter == 10) {
                            //                 ($img1["Images${counter}"] = $image_data_new->link);
                            //             } else if ($counter == 13) {
                            //                 ($img1["Images${counter}"] = $image_data_new->link);
                            //             } else if ($counter == 16) {
                            //                 ($img1["Images${counter}"] = $image_data_new->link);
                            //             } else if ($counter == 19) {
                            //                 ($img1["Images${counter}"] = $image_data_new->link);
                            //             }
                            //         }
                            //         $catalog_for_cliqnshop[$key1]['images'][$catalog_data['asin']] = $img1;
                            //     }
                            // }

                            // if ($key3 <= 10 && $images->height >= 1000 && $images->height <= 2000) {
                            //     $catalog_for_cliqnshop[$key1]['images'][$catalog_data['asin']]['image' . $key3 + 1] = $images->link;
                            // }
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
                    $catalog_for_cliqnshop[$key1]['height'] = isset($catalog_data['height']) ? round($catalog_data['height'], 2) : '';
                    // $catalog_for_cliqnshop[$key1]['weight']     = isset($catalog_data['weight']) ? $catalog_data['weight'] : '';
                    // $catalog_for_cliqnshop[$key1]['price_US']      = isset($productPrice1[$key1]) ? $productPrice1[$key1] : '';
                    if (isset($catalog_data['weight'])) {

                        $price_convert = new PriceConversion();
                        $catalog_for_cliqnshop[$key1]['price'] = $price_convert->$price_conversion_method($catalog_data['weight'], $price[$key1]);
                    }
                }
            }
            
            foreach ($catalog_for_cliqnshop as $cliqnshop_catalog) {
                $obj = $cliqnshop_catalog['item_package_dimensions'] = isset($cliqnshop_catalog['item_package_dimensions']) ? $cliqnshop_catalog['item_package_dimensions'] : '';
                $length_package_dimension =  isset($obj[0]->length->value) ? $obj[0]->length->value : '';
                $width_package_dimension =  isset($obj[0]->width->value) ? $obj[0]->width->value : '';
                $height_package_dimension =  isset($obj[0]->height->value) ? $obj[0]->height->value : '';

                 $ignore_cat = DB::connection('cliqnshop')->table('cns_ban_category')->where('site_id',$siteId)->pluck('category_code')->toArray();

                 $ignore_brand = DB::connection('cliqnshop')->table('cns_ban_brand')->where('site_id',$siteId)->pluck('brand')->toArray();

                 if ($ignore_brand == [])
                 {
                    $ignore_brand = ['Dame','Maude'];
                 }
                 
                 $ignore_brand_for_cliqnshop = ucwords(str_replace(',', '|', implode(',',$ignore_brand)), '|');

                 $ignore_asin = DB::connection('cliqnshop')->table('cns_ban_asin')->where('site_id',$siteId)->pluck('asin')->toArray();

                 if ($ignore_asin == [])
                 {
                    $ignore_asin = ['B00GGXW720','B09JJLQS7S'];
                 }

                 $ignore_asin_for_cliqnshop = ucwords(str_replace(',', '|', implode(',',$ignore_asin)), '|');

                if (isset($cliqnshop_catalog['price']) 
                && $length_package_dimension !== '' 
                && $length_package_dimension < 25 
                && $width_package_dimension !== '' 
                && $width_package_dimension < 25 
                && $height_package_dimension !== '' 
                && $height_package_dimension < 25 
                && isset($cliqnshop_catalog['images']) 
                && !in_array($cliqnshop_catalog['category_code'],$ignore_cat,true) 
                && preg_match("(" . strtolower($ignore_brand_for_cliqnshop) . ")", strtolower($cliqnshop_catalog['brand'])) !== 1 
                && preg_match("(" . strtolower($ignore_asin_for_cliqnshop) . ")", strtolower($cliqnshop_catalog['asin'])) !== 1) {
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
                    $editor = 'cns_search_from_'.$site;

                    $cliqnshop = new CliqnshopCataloginsert();
                    $cliqnshop->insertdata_cliqnshop($siteId, $category, $asin, $item_name, $brand, $brand_label, $color_key, $label, $length_unit, $length_value, $width_unit, $width_value, $Price_US_IN, $image_array, $searchKey, $short_description, $long_description, $generic_keywords,$editor, $display_code);
                       }
                    }
                }
            }
        }
    }
}
