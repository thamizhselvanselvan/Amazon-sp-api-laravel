<?php

namespace App\Services\AWS_Business_API\Search_Product_API;

use App\Models\Mws_region;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;
use App\Services\AWS_Business_API\AWS_POC\Search_Product_Request;
use App\Services\Catalog\PriceConversion;
use App\Services\Cliqnshop\CliqnshopCataloginsert;
use App\Services\SP_API\API\NewCatalog;
use Illuminate\Support\Facades\DB;

class Search_Product
{
    public function SearchProductByKey($searchKey, $site)
    {
        $productSearchApi = new Search_Product_Request();
        $getProducts = $productSearchApi->getASIN($searchKey, 'key');

        $siteIds = DB::connection('cliqnshop')->table('mshop_locale_site')->pluck('siteid');
        foreach ($siteIds as $siteId) {
            $source = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $siteId)->pluck('code')->toArray();
            if ($source[0] == $site) {
                $display_code = 1;
            } else {
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
            $product_type = '';
            $cliqnshop = new CliqnshopCataloginsert();

            if ($source[0] == 'in') {
                $ignore_key = DB::connection('cliqnshop')->table('cns_ban_keywords')->where('site_id', $siteId)->pluck('keyword')->toArray();
                if ($ignore_key == []) {
                    $ignore_key = ['Gun', 'Revolver', 'Pistol'];
                }
                $price_conversion_method = 'USAToINDB2C';
                $ignore_key_for_cliqnshop = ucwords(str_replace(',', '|', implode(',', $ignore_key)), '|');
            }

            if ($source[0] == 'uae') {
                $ignore_key = DB::connection('cliqnshop')->table('cns_ban_keywords')->where('site_id', $siteId)->pluck('keyword')->toArray();
                if ($ignore_key == []) {
                    $ignore_key = ['Radio', 'Walkie', 'Talkies'];
                }
                $price_conversion_method = 'USATOUAE';
                $ignore_key_for_cliqnshop = ucwords(str_replace(',', '|', implode(',', $ignore_key)), '|');
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
                foreach ($array_prices as $key1 => $price) {
                    if (!is_array($asin)) {
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
                                    $catalog_for_cliqnshop[$key1]['item_package_dimensions'] = isset($package_dimensions->item_package_dimensions) ? $package_dimensions->item_package_dimensions : '';
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
                            if ($key2 == 'relationships') {
                                $array = json_decode($catalog);
                                foreach ($array[0]->relationships as $key => $relationship) {
                                    if (isset($relationship->variationTheme->attributes)) {
                                        $attributes = $relationship->variationTheme->attributes;

                                        if (in_array('color', $attributes)) {
                                            if (isset($relationship->parentAsins[0])) {
                                                $parentAsin = $relationship->parentAsins[0];
                                                $catalog_for_cliqnshop[$key1]['parentAsin'] = $parentAsin;
                                                $product_type = 'parent';
                                            } else {
                                                $parentAsin = '';
                                            }

                                            if ($parentAsin == '') {
                                                if (isset($relationship->childAsins)) {
                                                    $child_asins = $relationship->childAsins;
                                                    $catalog_for_cliqnshop[$key1]['child_asins'] = $child_asins;
                                                    $product_type = 'child';
                                                }
                                            }

                                            $variant = true;
                                        } else {
                                            $variant = false;
                                        }
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
                                if ($price_conversion_method == 'USATOUAE')
                                {
                                $catalog_for_cliqnshop[$key1]['price'] = $price_convert->$price_conversion_method($catalog_data['weight'], $price[$key1] - ($price[$key1] * (10/100)));
                                }
                                else {
                                    $catalog_for_cliqnshop[$key1]['price'] = $price_convert->$price_conversion_method($catalog_data['weight'], $price[$key1]);
                                }
                            }
                        }
                    }

                    foreach ($catalog_for_cliqnshop as $cliqnshop_catalog) {
                        $parent = isset($cliqnshop_catalog['parentAsin']) ? $cliqnshop_catalog['parentAsin'] : '';
                        $child = isset($cliqnshop_catalog['child_asins']) ? $cliqnshop_catalog['child_asins'] : '';
                        $obj = $cliqnshop_catalog['item_package_dimensions'] = isset($cliqnshop_catalog['item_package_dimensions']) ? $cliqnshop_catalog['item_package_dimensions'] : '';
                        $length_package_dimension = isset($obj[0]->length->value) ? $obj[0]->length->value : '';
                        $width_package_dimension = isset($obj[0]->width->value) ? $obj[0]->width->value : '';
                        $height_package_dimension = isset($obj[0]->height->value) ? $obj[0]->height->value : '';

                        $ignore_cat = DB::connection('cliqnshop')->table('cns_ban_category')->where('site_id', $siteId)->pluck('category_code')->toArray();

                        $ignore_brand = DB::connection('cliqnshop')->table('cns_ban_brand')->where('site_id', $siteId)->pluck('brand')->toArray();

                        if ($ignore_brand == []) {
                            $ignore_brand = ['Dame', 'Maude'];
                        }

                        $ignore_brand_for_cliqnshop = ucwords(str_replace(',', '|', implode(',', $ignore_brand)), '|');

                        $ignore_asin = DB::connection('cliqnshop')->table('cns_ban_asin')->where('site_id', $siteId)->pluck('asin')->toArray();

                        if ($ignore_asin == []) {
                            $ignore_asin = ['B00GGXW720', 'B09JJLQS7S'];
                        }

                        $ignore_asin_for_cliqnshop = ucwords(str_replace(',', '|', implode(',', $ignore_asin)), '|');

                        if (isset($cliqnshop_catalog['price'])
                            && $length_package_dimension !== ''
                            && $length_package_dimension < 25
                            && $width_package_dimension !== ''
                            && $width_package_dimension < 25
                            && $height_package_dimension !== ''
                            && $height_package_dimension < 25
                            && isset($cliqnshop_catalog['images'])
                            && !in_array($cliqnshop_catalog['category_code'], $ignore_cat, true)
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
                            $editor = 'cns_search_from_' . $site;
                            if ($parent == '' && $child == '') {

                                $cliqnshop->insertdata_cliqnshop($siteId, $category, $asin, $item_name, $brand, $brand_label, $color_key, $label, $length_unit, $length_value, $width_unit, $width_value, $Price_US_IN, $image_array, $searchKey, $short_description, $long_description, $generic_keywords, $editor, $display_code);
                            } elseif ($parent !== '') {
                                $this->Parent_Child_Asin($parent, $country_code, $seller_id, $aws_id, $token, $price_conversion_method, $siteId, $site, $display_code, $cliqnshop_catalog['price'], $product_type, $searchKey);

                                DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $parent)->update(['type' => 'select']);

                                $cliqnshop->insertdata_cliqnshop($siteId, $category, $asin, $item_name, $brand, $brand_label, $color_key, $label, $length_unit, $length_value, $width_unit, $width_value, $Price_US_IN, $image_array, $searchKey, $short_description, $long_description, $generic_keywords, $editor, $display_code);

                                $get_parent_id = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $parent)->value('id');

                                $get_varient_id = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $asin)->value('id');

                                $color_key_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $color_key));

                                $color = str_replace(' ', '', substr(strtolower($color_key_replaced), 0, 10));

                                $get_attribute_id = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $siteId)->where('code', $color)->value('id');

                                if ($get_parent_id !== null && $get_attribute_id !== null) {
                                    $variant_product_list = [
                                        'siteid' => $siteId,
                                        'parentid' => $get_parent_id,
                                        'key' => 'product|default|' . $get_varient_id,
                                        'type' => 'default',
                                        'domain' => 'product',
                                        'refid' => $get_varient_id,
                                        // 'start' => NULL,
                                        // 'end' => NULL,
                                        'config' => '[]',
                                        // 'pos' => 0,
                                        'status' => $display_code,
                                        'mtime' => now(),
                                        'ctime' => now(),
                                        'editor' => $editor,
                                    ];

                                    DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                                        $variant_product_list,
                                        ['unq_msproli_pid_dm_ty_rid_sid'],
                                        ['siteid', 'parentid', 'key', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                                    );

                                    $variant_list = [
                                        'siteid' => $siteId,
                                        'parentid' => $get_varient_id,
                                        'key' => 'attribute|variant|' . $get_attribute_id,
                                        'type' => 'variant',
                                        'domain' => 'attribute',
                                        'refid' => $get_attribute_id,
                                        // 'start' => NULL,
                                        // 'end' => NULL,
                                        'config' => '[]',
                                        // 'pos' => 0,
                                        'status' => $display_code,
                                        'mtime' => now(),
                                        'ctime' => now(),
                                        'editor' => $editor,
                                    ];

                                    DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                                        $variant_list,
                                        ['unq_msproli_pid_dm_ty_rid_sid'],
                                        ['siteid', 'parentid', 'key', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                                    );
                                }
                            } elseif ($child !== '') {
                    
                                    $width_value = '';
                                    $length_value = '';
                                
                                $cliqnshop->insertdata_cliqnshop($siteId, $category, $asin, $item_name, $brand, $brand_label, $color_key, $label, $length_unit, $length_value, $width_unit, $width_value, $Price_US_IN, $image_array, $searchKey, $short_description, $long_description, $generic_keywords, $editor, $display_code);

                                DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $asin)->update(['type' => 'select']);

                                foreach ($child as $key => $variant) {

                                    $this->Parent_Child_Asin($variant, $country_code, $seller_id, $aws_id, $token, $price_conversion_method, $siteId, $site, $display_code, $cliqnshop_catalog['price'], $product_type, $searchKey);

                                    $get_parent_id = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $asin)->value('id');

                                    $get_varient_id = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $variant)->value('id');

                                    if ($get_parent_id !== null && $get_varient_id !== null) {
                                        $variant_product_list = [
                                            'siteid' => $siteId,
                                            'parentid' => $get_parent_id,
                                            'key' => 'product|default|' . $get_varient_id,
                                            'type' => 'default',
                                            'domain' => 'product',
                                            'refid' => $get_varient_id,
                                            // 'start' => NULL,
                                            // 'end' => NULL,
                                            'config' => '[]',
                                            // 'pos' => 0,
                                            'status' => $display_code,
                                            'mtime' => now(),
                                            'ctime' => now(),
                                            'editor' => $editor,
                                        ];

                                        DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                                            $variant_product_list,
                                            ['unq_msproli_pid_dm_ty_rid_sid'],
                                            ['siteid', 'parentid', 'key', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function Parent_Child_Asin($parent_child_asin, $countryCode, $sellerId, $awsId, $token, $priceConversionMethod, $siteId, $site, $displayCode, $Price, $Product_type, $searchKey)
    {
        if ($Product_type == 'child') {
            $ProductPriceRequest = new ProductsRequest();
            $productPrice = $ProductPriceRequest->getASINpr($parent_child_asin);
            $prices = isset($productPrice->includedDataTypes->OFFERS) ? $productPrice->includedDataTypes->OFFERS : '';

            $productAsin = [];
            $productPrice1 = [];
            if (!is_array($parent_child_asin)) {
                $parent_child_asin = array($parent_child_asin);
            }
            foreach ($parent_child_asin as $key => $parent_child) {
                if (isset($prices[0])) {
                    $productAsin[] = $parent_child;
                    $productPrice1[] = isset($prices[0]->listPrice->value) != '' ? $prices[0]->listPrice->value->amount : $prices[0]->price->value->amount;
                }
            }
        } elseif ($Product_type == 'parent') {
            $productAsin[] = $parent_child_asin;
            $productPrice1[] = '';
            if (!is_array($productAsin) || !is_array($productPrice1)) {
                $productAsin = array($productAsin);
                $productPrice1 = array($productPrice1);
            }
        }

        $arrayAsins = array_chunk($productAsin, 10);
        $arrayPrices = array_chunk($productPrice1, 10);

        foreach ($arrayAsins as $key => $parent_child) {
            foreach ($arrayPrices as $key1 => $price) {
                if (!is_array($parent_child)) {
                    $parent_child = array($parent_child);
                }

                $catalogForCliqnshop = new NewCatalog();
                $catalogs = $catalogForCliqnshop->FetchDataFromCatalog($parent_child, $countryCode, $sellerId, $token, $awsId);

                $catalogForCliqnshop = [];

                foreach ($catalogs as $key1 => $catalogData) {
                    foreach ($catalogData as $key2 => $catalog) {
                        if ($key2 == 'attributes') {
                            $attributes = json_decode($catalog);
                            if (array_key_exists('bullet_point', (array) $attributes)) {
                                $catalogForCliqnshop[$key1]['short_description'] = isset($attributes->bullet_point[0]->value) ? $attributes->bullet_point[0]->value : '';
                                $longDesc = '';
                                foreach ($attributes->bullet_point as $bulletPoint) {
                                    $longDesc .= "<p>" . (isset($bulletPoint->value) ? $bulletPoint->value : '');
                                    $catalogForCliqnshop[$key1]['long_description'] = $longDesc;
                                }
                            }

                            if (array_key_exists('generic_keyword', (array) $attributes)) {
                                $catalogForCliqnshop[$key1]['generic_keyword'] = isset($attributes->generic_keyword[0]->value) ? $attributes->generic_keyword[0]->value : '';
                                $generKey = [];
                                foreach ($attributes->generic_keyword as $generic) {
                                    $generKey[] = preg_split("/[,;]/", $generic->value);
                                    $catalogForCliqnshop[$key1]['generic_keywords'] = $generKey;
                                }
                            }
                            if (array_key_exists('item_package_dimensions', (array) $attributes)) {
                                $packageDimensions = json_decode($catalog);
                                $catalogForCliqnshop[$key1]['item_package_dimensions'] = isset($packageDimensions->item_package_dimensions) ? $packageDimensions->item_package_dimensions : '';
                            }
                        }

                        if ($key2 == 'browseClassification') {
                            $classifications = json_decode($catalog);
                            if (array_key_exists('classificationId', (array) $classifications)) {
                                $catalogForCliqnshop[$key1]['category_code'] = $classifications->classificationId;
                            }
                        }
                        if ($key2 == 'images') {
                            $catalog_images = json_decode($catalog);
                            foreach ($catalog_images[0]->images as $key3 => $images) {

                                if (isset($catalog_images[0]->images)) {
                                    if ($key3 <= 9 && $images->height > 500 || $images->width > 500) {
                                        $catalogForCliqnshop[$key1]['images'][$catalogData['asin']]['image' . $key3 + 1] = $images->link;
                                    }
                                }
                            }
                        }

                        if ($key2 == 'relationships') {
                            $array = json_decode($catalog);
                            $product_type = '';
                            foreach ($array[0]->relationships as $key => $relationship) {
                                if (isset($relationship->variationTheme->attributes)) {
                                    $attributes = $relationship->variationTheme->attributes;

                                    if (in_array('color', $attributes)) {
                                        if (isset($relationship->parentAsins[0])) {
                                            $parentAsin = $relationship->parentAsins[0];
                                            $catalogForCliqnshop[$key1]['parentAsin'] = $parentAsin;
                                            $product_type = 'parent';
                                        } else {
                                            $parentAsin = '';
                                        }

                                        if ($parentAsin == '') {
                                            if (isset($relationship->childAsins)) {
                                                $child_asins = $relationship->childAsins;
                                                $catalogForCliqnshop[$key1]['child_asins'] = $child_asins;
                                                $product_type = 'child';
                                            }
                                        }

                                        $variant = true;
                                    } else {
                                        $variant = false;
                                    }
                                }

                            }

                        }

                        $catalogForCliqnshop[$key1]['asin'] = $catalogData['asin'];
                        $catalogForCliqnshop[$key1]['itemName'] = isset($catalogData['itemName']) ? $catalogData['itemName'] : '';
                        $catalogForCliqnshop[$key1]['brand'] = isset($catalogData['brand']) ? $catalogData['brand'] : '';
                        $catalogForCliqnshop[$key1]['color'] = isset($catalogData['color']) ? $catalogData['color'] : '';
                        $catalogForCliqnshop[$key1]['unit'] = isset($catalogData['unit']) ? $catalogData['unit'] : '';
                        $catalogForCliqnshop[$key1]['length'] = isset($catalogData['length']) ? round($catalogData['length'], 2) : '';
                        $catalogForCliqnshop[$key1]['width'] = isset($catalogData['width']) ? round($catalogData['width'], 2) : '';
                        $catalogForCliqnshop[$key1]['height'] = isset($catalogData['height']) ? round($catalogData['height'], 2) : '';
                        if ($Product_type == 'child') {
                            if (isset($catalogData['weight'])) {
                                $priceConvert = new PriceConversion();
                               if ($priceConversionMethod == 'USATOUAE')
                               {
                                $catalogForCliqnshop[$key1]['price'] = $priceConvert->$priceConversionMethod($catalogData['weight'], $price[$key1] - ($price[$key1] * 10/100));
                               }
                               else {
                                $catalogForCliqnshop[$key1]['price'] = $priceConvert->$priceConversionMethod($catalogData['weight'], $price[$key1]);
                               }
                            }
                        }
                    }
                }

                foreach ($catalogForCliqnshop as $cliqnshopCatalog) {

                    $parent = isset($cliqnshopCatalog['parentAsin']) ? $cliqnshopCatalog['parentAsin'] : '';
                    $child = isset($cliqnshopCatalog['child_asins']) ? $cliqnshopCatalog['child_asins'] : '';

                    $obj = $cliqnshopCatalog['item_package_dimensions'] = isset($cliqnshopCatalog['item_package_dimensions']) ? $cliqnshopCatalog['item_package_dimensions'] : '';
                    $lengthPackageDimension = isset($obj[0]->length->value) ? $obj[0]->length->value : '';
                    $widthPackageDimension = isset($obj[0]->width->value) ? $obj[0]->width->value : '';
                    $heightPackageDimension = isset($obj[0]->height->value) ? $obj[0]->height->value : '';

                    $ignoreCat = DB::connection('cliqnshop')->table('cns_ban_category')->where('site_id', $siteId)->pluck('category_code')->toArray();

                    $ignoreBrand = DB::connection('cliqnshop')->table('cns_ban_brand')->where('site_id', $siteId)->pluck('brand')->toArray();

                    if ($ignoreBrand == []) {
                        $ignoreBrand = ['Dame', 'Maude'];
                    }

                    $ignoreBrandForCliqnshop = ucwords(str_replace(',', '|', implode(',', $ignoreBrand)), '|');

                    $ignoreAsin = DB::connection('cliqnshop')->table('cns_ban_asin')->where('site_id', $siteId)->pluck('asin')->toArray();

                    if ($ignoreAsin == []) {
                        $ignoreAsin = ['B00GGXW720', 'B09JJLQS7S'];
                    }

                    $ignoreAsinForCliqnshop = ucwords(str_replace(',', '|', implode(',', $ignoreAsin)), '|');
                    if ($Product_type == 'parent') {
                        $cliqnshopCatalog['price'] = $Price;
                    }

                    if (
                        isset($cliqnshopCatalog['price'])
                        && $lengthPackageDimension !== ''
                        && $lengthPackageDimension < 25
                        && $widthPackageDimension !== ''
                        && $widthPackageDimension < 25
                        && $heightPackageDimension !== ''
                        && $heightPackageDimension < 25
                        && isset($cliqnshopCatalog['images'])
                        && !in_array($cliqnshopCatalog['category_code'], $ignoreCat, true)
                        && preg_match("(" . strtolower($ignoreBrandForCliqnshop) . ")", strtolower($cliqnshopCatalog['brand'])) !== 1
                        && preg_match("(" . strtolower($ignoreAsinForCliqnshop) . ")", strtolower($cliqnshopCatalog['asin'])) !== 1
                    ) {
                        $category = $cliqnshopCatalog['category_code'] ?? 'demo-new';
                        $asin = $cliqnshopCatalog['asin'];
                        $itemName = $cliqnshopCatalog['itemName'];
                        $brand = $cliqnshopCatalog['brand'];
                        $brandLabel = $cliqnshopCatalog['brand'];
                        $colorKey = $cliqnshopCatalog['color'];
                        $label = $cliqnshopCatalog['color'];
                        $lengthUnit = $cliqnshopCatalog['unit'];
                        $lengthValue = $cliqnshopCatalog['length'];
                        $widthUnit = $cliqnshopCatalog['unit'];
                        $widthValue = $cliqnshopCatalog['width'];
                        $priceUsIn = $cliqnshopCatalog['price'];
                        $imageArray = $cliqnshopCatalog['images'];
                        $shortDescription = $cliqnshopCatalog['short_description'] ?? '';
                        $longDescription = $cliqnshopCatalog['long_description'] ?? '';
                        $genericKeywords = $cliqnshopCatalog['generic_keywords'] ?? '';
                        $editor = 'cns_search_from_' . $site;

                        if ($Product_type == 'parent') {
                            $widthValue = '';
                            $lengthValue = '';
                        }

                        $cliqnshop = new CliqnshopCataloginsert();
                        $cliqnshop->insertdata_cliqnshop(
                            $siteId,
                            $category,
                            $asin,
                            $itemName,
                            $brand,
                            $brandLabel,
                            $colorKey,
                            $label,
                            $lengthUnit,
                            $lengthValue,
                            $widthUnit,
                            $widthValue,
                            $priceUsIn,
                            $imageArray,
                            $searchKey,
                            $shortDescription,
                            $longDescription,
                            $genericKeywords,
                            $editor,
                            $displayCode
                        );

                        if ($child !== '') {
                            foreach ($child as $key => $variant) {

                                $this->Child_Asin($variant, $countryCode, $sellerId, $awsId, $token, $priceConversionMethod, $siteId, $site, $displayCode, $Price, $product_type, $searchKey);

                                $get_parent_id = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $asin)->value('id');

                                $get_varient_id = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $variant)->value('id');

                                if ($get_parent_id !== null && $get_varient_id !== null) {
                                    $variant_product_list = [
                                        'siteid' => $siteId,
                                        'parentid' => $get_parent_id,
                                        'key' => 'product|default|' . $get_varient_id,
                                        'type' => 'default',
                                        'domain' => 'product',
                                        'refid' => $get_varient_id,
                                        // 'start' => NULL,
                                        // 'end' => NULL,
                                        'config' => '[]',
                                        // 'pos' => 0,
                                        'status' => $displayCode,
                                        'mtime' => now(),
                                        'ctime' => now(),
                                        'editor' => $editor,
                                    ];

                                    DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                                        $variant_product_list,
                                        ['unq_msproli_pid_dm_ty_rid_sid'],
                                        ['siteid', 'parentid', 'key', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                                    );
                                }
                            }
                        }

                        if ($Product_type == 'child') {

                            $color_key_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $colorKey));

                            $color = str_replace(' ', '', substr(strtolower($color_key_replaced), 0, 10));

                            $get_attribute_id = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $siteId)->where('code', $color)->value('id');

                            $get_varient_id = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $asin)->value('id');

                            if ($get_attribute_id !== null && $get_varient_id !== null) {
                                $variant_list = [
                                    'siteid' => $siteId,
                                    'parentid' => $get_varient_id,
                                    'key' => 'attribute|variant|' . $get_attribute_id,
                                    'type' => 'variant',
                                    'domain' => 'attribute',
                                    'refid' => $get_attribute_id,
                                    // 'start' => NULL,
                                    // 'end' => NULL,
                                    'config' => '[]',
                                    // 'pos' => 0,
                                    'status' => $displayCode,
                                    'mtime' => now(),
                                    'ctime' => now(),
                                    'editor' => $editor,
                                ];

                                DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                                    $variant_list,
                                    ['unq_msproli_pid_dm_ty_rid_sid'],
                                    ['siteid', 'parentid', 'key', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                                );
                            }

                        }

                    }
                }
            }
        }
    }

    public function Child_Asin($parent_child_asin, $countryCode, $sellerId, $awsId, $token, $priceConversionMethod, $siteId, $site, $displayCode, $Price, $Product_type, $searchKey)
    {
        if ($Product_type == 'child') {
            $ProductPriceRequest = new ProductsRequest();
            $productPrice = $ProductPriceRequest->getASINpr($parent_child_asin);
            $prices = isset($productPrice->includedDataTypes->OFFERS) ? $productPrice->includedDataTypes->OFFERS : '';

            $productAsin = [];
            $productPrice1 = [];
            if (!is_array($parent_child_asin)) {
                $parent_child_asin = array($parent_child_asin);
            }
            foreach ($parent_child_asin as $key => $parent_child) {
                if (isset($prices[0])) {
                    $productAsin[] = $parent_child;
                    $productPrice1[] = isset($prices[0]->listPrice->value) != '' ? $prices[0]->listPrice->value->amount : $prices[0]->price->value->amount;
                }
            }
        } elseif ($Product_type == 'parent') {
            $productAsin[] = $parent_child_asin;
            $productPrice1[] = '';
            if (!is_array($productAsin) || !is_array($productPrice1)) {
                $productAsin = array($productAsin);
                $productPrice1 = array($productPrice1);
            }
        }

        $arrayAsins = array_chunk($productAsin, 10);
        $arrayPrices = array_chunk($productPrice1, 10);

        foreach ($arrayAsins as $key => $parent_child) {
            foreach ($arrayPrices as $key1 => $price) {
                if (!is_array($parent_child)) {
                    $parent_child = array($parent_child);
                }

                $catalogForCliqnshop = new NewCatalog();
                $catalogs = $catalogForCliqnshop->FetchDataFromCatalog($parent_child, $countryCode, $sellerId, $token, $awsId);

                $catalogForCliqnshop = [];

                foreach ($catalogs as $key1 => $catalogData) {
                    foreach ($catalogData as $key2 => $catalog) {
                        if ($key2 == 'attributes') {
                            $attributes = json_decode($catalog);
                            if (array_key_exists('bullet_point', (array) $attributes)) {
                                $catalogForCliqnshop[$key1]['short_description'] = isset($attributes->bullet_point[0]->value) ? $attributes->bullet_point[0]->value : '';
                                $longDesc = '';
                                foreach ($attributes->bullet_point as $bulletPoint) {
                                    $longDesc .= "<p>" . (isset($bulletPoint->value) ? $bulletPoint->value : '');
                                    $catalogForCliqnshop[$key1]['long_description'] = $longDesc;
                                }
                            }

                            if (array_key_exists('generic_keyword', (array) $attributes)) {
                                $catalogForCliqnshop[$key1]['generic_keyword'] = isset($attributes->generic_keyword[0]->value) ? $attributes->generic_keyword[0]->value : '';
                                $generKey = [];
                                foreach ($attributes->generic_keyword as $generic) {
                                    $generKey[] = preg_split("/[,;]/", $generic->value);
                                    $catalogForCliqnshop[$key1]['generic_keywords'] = $generKey;
                                }
                            }
                            if (array_key_exists('item_package_dimensions', (array) $attributes)) {
                                $packageDimensions = json_decode($catalog);
                                $catalogForCliqnshop[$key1]['item_package_dimensions'] = isset($packageDimensions->item_package_dimensions) ? $packageDimensions->item_package_dimensions : '';
                            }
                        }

                        if ($key2 == 'browseClassification') {
                            $classifications = json_decode($catalog);
                            if (array_key_exists('classificationId', (array) $classifications)) {
                                $catalogForCliqnshop[$key1]['category_code'] = $classifications->classificationId;
                            }
                        }
                        if ($key2 == 'images') {
                            $catalog_images = json_decode($catalog);
                            foreach ($catalog_images[0]->images as $key3 => $images) {

                                if (isset($catalog_images[0]->images)) {
                                    if ($key3 <= 9 && $images->height > 500 || $images->width > 500) {
                                        $catalogForCliqnshop[$key1]['images'][$catalogData['asin']]['image' . $key3 + 1] = $images->link;
                                    }
                                }
                            }
                        }

                        $catalogForCliqnshop[$key1]['asin'] = $catalogData['asin'];
                        $catalogForCliqnshop[$key1]['itemName'] = isset($catalogData['itemName']) ? $catalogData['itemName'] : '';
                        $catalogForCliqnshop[$key1]['brand'] = isset($catalogData['brand']) ? $catalogData['brand'] : '';
                        $catalogForCliqnshop[$key1]['color'] = isset($catalogData['color']) ? $catalogData['color'] : '';
                        $catalogForCliqnshop[$key1]['unit'] = isset($catalogData['unit']) ? $catalogData['unit'] : '';
                        $catalogForCliqnshop[$key1]['length'] = isset($catalogData['length']) ? round($catalogData['length'], 2) : '';
                        $catalogForCliqnshop[$key1]['width'] = isset($catalogData['width']) ? round($catalogData['width'], 2) : '';
                        $catalogForCliqnshop[$key1]['height'] = isset($catalogData['height']) ? round($catalogData['height'], 2) : '';
                        if ($Product_type == 'child') {
                            if (isset($catalogData['weight'])) {
                                $priceConvert = new PriceConversion();
                               if ($priceConversionMethod == 'USATOUAE')
                               {
                                $catalogForCliqnshop[$key1]['price'] = $priceConvert->$priceConversionMethod($catalogData['weight'], $price[$key1] - ($price[$key1] * 10/100));
                               }
                               else {
                                $catalogForCliqnshop[$key1]['price'] = $priceConvert->$priceConversionMethod($catalogData['weight'], $price[$key1]);
                               }
                            }
                        }
                    }
                }

                foreach ($catalogForCliqnshop as $cliqnshopCatalog) {
                    $obj = $cliqnshopCatalog['item_package_dimensions'] = isset($cliqnshopCatalog['item_package_dimensions']) ? $cliqnshopCatalog['item_package_dimensions'] : '';
                    $lengthPackageDimension = isset($obj[0]->length->value) ? $obj[0]->length->value : '';
                    $widthPackageDimension = isset($obj[0]->width->value) ? $obj[0]->width->value : '';
                    $heightPackageDimension = isset($obj[0]->height->value) ? $obj[0]->height->value : '';

                    $ignoreCat = DB::connection('cliqnshop')->table('cns_ban_category')->where('site_id', $siteId)->pluck('category_code')->toArray();

                    $ignoreBrand = DB::connection('cliqnshop')->table('cns_ban_brand')->where('site_id', $siteId)->pluck('brand')->toArray();

                    if ($ignoreBrand == []) {
                        $ignoreBrand = ['Dame', 'Maude'];
                    }

                    $ignoreBrandForCliqnshop = ucwords(str_replace(',', '|', implode(',', $ignoreBrand)), '|');

                    $ignoreAsin = DB::connection('cliqnshop')->table('cns_ban_asin')->where('site_id', $siteId)->pluck('asin')->toArray();

                    if ($ignoreAsin == []) {
                        $ignoreAsin = ['B00GGXW720', 'B09JJLQS7S'];
                    }

                    $ignoreAsinForCliqnshop = ucwords(str_replace(',', '|', implode(',', $ignoreAsin)), '|');
                    if ($Product_type == 'parent') {
                        $cliqnshopCatalog['price'] = $Price;
                    }

                    if (
                        isset($cliqnshopCatalog['price'])
                        && $lengthPackageDimension !== ''
                        && $lengthPackageDimension < 25
                        && $widthPackageDimension !== ''
                        && $widthPackageDimension < 25
                        && $heightPackageDimension !== ''
                        && $heightPackageDimension < 25
                        && isset($cliqnshopCatalog['images'])
                        && !in_array($cliqnshopCatalog['category_code'], $ignoreCat, true)
                        && preg_match("(" . strtolower($ignoreBrandForCliqnshop) . ")", strtolower($cliqnshopCatalog['brand'])) !== 1
                        && preg_match("(" . strtolower($ignoreAsinForCliqnshop) . ")", strtolower($cliqnshopCatalog['asin'])) !== 1
                    ) {
                        $category = $cliqnshopCatalog['category_code'] ?? 'demo-new';
                        $asin = $cliqnshopCatalog['asin'];
                        $itemName = $cliqnshopCatalog['itemName'];
                        $brand = $cliqnshopCatalog['brand'];
                        $brandLabel = $cliqnshopCatalog['brand'];
                        $colorKey = $cliqnshopCatalog['color'];
                        $label = $cliqnshopCatalog['color'];
                        $lengthUnit = $cliqnshopCatalog['unit'];
                        $lengthValue = $cliqnshopCatalog['length'];
                        $widthUnit = $cliqnshopCatalog['unit'];
                        $widthValue = $cliqnshopCatalog['width'];
                        $priceUsIn = $cliqnshopCatalog['price'];
                        $imageArray = $cliqnshopCatalog['images'];
                        $shortDescription = $cliqnshopCatalog['short_description'] ?? '';
                        $longDescription = $cliqnshopCatalog['long_description'] ?? '';
                        $genericKeywords = $cliqnshopCatalog['generic_keywords'] ?? '';
                        $editor = 'cns_search_from_' . $site;

                        $cliqnshop = new CliqnshopCataloginsert();
                        $cliqnshop->insertdata_cliqnshop(
                            $siteId,
                            $category,
                            $asin,
                            $itemName,
                            $brand,
                            $brandLabel,
                            $colorKey,
                            $label,
                            $lengthUnit,
                            $lengthValue,
                            $widthUnit,
                            $widthValue,
                            $priceUsIn,
                            $imageArray,
                            $searchKey,
                            $shortDescription,
                            $longDescription,
                            $genericKeywords,
                            $editor,
                            $displayCode
                        );

                        $color_key_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $colorKey));

                        $color = str_replace(' ', '', substr(strtolower($color_key_replaced), 0, 10));

                        $get_attribute_id = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $siteId)->where('code', $color)->value('id');

                        $get_varient_id = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $siteId)->where('asin', $asin)->value('id');

                        if ($get_attribute_id !== null && $get_varient_id !== null) {
                            $variant_list = [
                                'siteid' => $siteId,
                                'parentid' => $get_varient_id,
                                'key' => 'attribute|variant|' . $get_attribute_id,
                                'type' => 'variant',
                                'domain' => 'attribute',
                                'refid' => $get_attribute_id,
                                // 'start' => NULL,
                                // 'end' => NULL,
                                'config' => '[]',
                                // 'pos' => 0,
                                'status' => $displayCode,
                                'mtime' => now(),
                                'ctime' => now(),
                                'editor' => $editor,
                            ];

                            DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                                $variant_list,
                                ['unq_msproli_pid_dm_ty_rid_sid'],
                                ['siteid', 'parentid', 'key', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                            );
                        }

                    }
                }
            }
        }
    }

}
