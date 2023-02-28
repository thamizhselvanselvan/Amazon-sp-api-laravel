<?php

namespace App\Services\Cliqnshop;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Cliqnshop\SKU_Generator;
use Exception;

class CliqnshopCataloginsert
{
    public function insertdata_cliqnshop($site_id, $category, $asin,  $item_name,  $brand,  $brand_label,  $color_key,  $label,  $length_unit,  $length_value,  $width_unit,  $width_value,  $Price_US_IN,  $image, $keyword,  $short_description,  $long_description, $generic_keywords)
    {
        Log::alert($asin . ' - ' . $category);
        try {
            $display_code = '1';
            if ($Price_US_IN == '0' || $Price_US_IN == '' || $image == '') {
                $display_code = '0';
            }

            $string_original = str_replace('&', 'and', $item_name);

            $item_name_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $string_original));



            //delete price id old
            $product_id_asin  = '';
            $get_product_asin = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $site_id)->where('code', $asin)
                ->pluck('id')->ToArray();
            if (isset($get_product_asin[0])) {
                $product_id_asin = $get_product_asin[0];
            }

            $price_id_mpl = '';
            $price_remove_mpl = DB::connection('cliqnshop')->table('mshop_product_list')
                ->where(['parentid' => $product_id_asin, 'domain' => 'price', 'type' => 'default', 'siteid' => $site_id])
                ->select('refid')->get();
            foreach ($price_remove_mpl as $val) {
                if (isset($price_remove_mpl[0]->refid)) {
                    $price_id_mpl = $price_remove_mpl[0]->refid;
                }

                DB::connection('cliqnshop')->table('mshop_product_list')
                    ->where(['parentid' => $product_id_asin, 'domain' => 'price', 'type' => 'default', 'siteid' => $site_id])
                    ->select('refid')->delete();

                $price_currency = '';
                $price_table = DB::connection('cliqnshop')->table('mshop_price')
                    ->where(['id' => $price_id_mpl, 'siteid' => $site_id])
                    ->select('currencyid')->get();
                if (isset($price_table[0]->currencyid)) {
                    $price_currency = $price_table[0]->currencyid;
                }
                DB::connection('cliqnshop')->table('mshop_price')
                    ->where(['id' => $price_id_mpl, 'siteid' => $site_id])
                    ->select('currencyid')->delete();


                DB::connection('cliqnshop')->table('mshop_index_price')
                    ->where(['prodid' => $product_id_asin, 'currencyid' => $price_currency, 'siteid' => $site_id])
                    ->select('currencyid')->delete();
            }

            //delete price id old end

            //site_id get
            $currency = DB::connection('cliqnshop')->table('mshop_locale')->select('currencyid')->where('siteid', $site_id)->where('status', '1')->get();
            $currency_code = $currency['0']->currencyid;

            $date_time = Carbon::now('Asia/Kolkata');

            $sku_genrator = new SKU_Generator();
            $item_name_trimmed = substr($item_name, 0, 500);
            $item_url_trimmed = substr($item_name_replaced, 0, 500);
            $product_data = [
                'siteid' => $site_id,
                // 'dataset' => '',
                'type' => 'default',
                'code' => $sku_genrator->generateSKU('CNS', $asin),
                'asin' => $asin, //ASIN
                'label' => $item_name_trimmed,
                'url' => mb_strtolower(str_replace(array('&', '<', '>', ';', ' ', ',', '’', '-'), '_', $item_url_trimmed)),
                'config' => '[]',
                // 'start' => NULL,
                // 'end' => NULL,
                'scale' => 1,
                // 'rating' => 0.00,
                // 'ratings' => 0,
                'instock' => 1,
                // 'target' => '',
                'status' => $display_code,
                'mtime' => $date_time,
                'ctime' => $date_time,
                'editor' => 'App360',
            ];
            DB::connection('cliqnshop')->table('mshop_product')->upsert(
                $product_data,
                ['unq_mspro_siteid_code_asin_sid'],
                ['code', 'label', 'url', 'status', 'mtime', 'editor', 'siteid', 'type', 'config', 'scale']
            );

            $get_product = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $product_data['siteid'])->where('code', $product_data['code'])->where('asin', $product_data['asin'])
                ->pluck('id')->ToArray();
            $get_product_id = $get_product[0];

            //brand (mshop_suplier)
            $brand_insert = [];
            $brand_insert['label'] = '';
            $get_brand_id = '';
            if ($brand != '') {
                $brand_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $brand));
                $brand_label_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $brand_label));
                $brand_insert = [
                    'siteid' => $site_id,
                    'code' =>  substr(strtolower($brand_replaced), 0, 10),
                    'label' => substr($brand_label_replaced, 0, 29),
                    // 'status' => 1,
                    // 'pos' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];

                DB::connection('cliqnshop')->table('mshop_supplier')->upsert($brand_insert, ['unq_mssup_code_sid'], ['siteid', 'code', 'label', 'mtime', 'editor']);

                $get_brand = DB::connection('cliqnshop')->table('mshop_supplier')->where('siteid', $brand_insert['siteid'])->where('code', $brand_insert['code'])
                    ->pluck('id')->ToArray();
                $get_brand_id = $get_brand[0];
            }

            //generic Keyword
            if (isset($generic_keywords)) {

                $gen_keyword_get_id = [];
                $gen_keyword = [];
                foreach ($generic_keywords as $values) {

                    foreach ($values as $val) {
                        $val_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $val));
                        $trim_keyword = substr($val_replaced, 0, 500);
                        $gen_keyword = [
                            'siteid' => $site_id,
                            'keyword' => str_replace("\xc2\xa0", " ", $trim_keyword),
                            'status' => 1,
                            'mtime' => $date_time,
                            'ctime' => $date_time,
                            'editor' => 'App360',
                        ];

                        DB::connection('cliqnshop')->table('mshop_keyword')->upsert($gen_keyword, ['unq_mskey_sid_keyword'], ['keyword', 'status', 'mtime']);
                        $gen_keyword_get_id = DB::connection('cliqnshop')->table('mshop_keyword')->where('siteid', $gen_keyword['siteid'])
                            ->where('keyword', $gen_keyword['keyword'])
                            ->pluck('id')->ToArray();

                        $genric_key_attribute = [
                            'siteid' => $site_id,
                            'parentid' => $get_product_id,
                            'key' => 'keyword|default|' . $gen_keyword_get_id['0'],
                            'type' => 'default',
                            'domain' => 'keyword',
                            'refid' => $gen_keyword_get_id['0'],
                            // 'start' => NULL,
                            // 'end' => NULL,
                            'config' => '[]',
                            // 'pos' => 0,
                            // 'status' => 1,
                            'mtime' => $date_time,
                            'ctime' => $date_time,
                            'editor' => 'App360',
                        ];

                        DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                            $genric_key_attribute,
                            ['unq_msproli_pid_dm_ty_rid_sid'],
                            ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                        );


                        $index_generic_key = [
                            'prodid' => $get_product_id,
                            'siteid' => $site_id,
                            'keyid' => $gen_keyword_get_id['0'],
                            'mtime' => $date_time,
                        ];
                        DB::connection('cliqnshop')->table('mshop_index_keyword')->upsert(
                            $index_generic_key,
                            ['unq_msindkey_pid_kid_sid'],
                            ['keyid', 'mtime']
                        );
                    }
                }
            }

            //color (mshop_attribute)
            $get_attribute_id = '';
            $attribute = [];
            $attribute['label'] = '';
            if ($color_key != '') {
                $color_key_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $color_key));
                $label_replaced = (preg_replace('/[^A-Za-z0-9\-]/', ' ', $label));
                $attribute = [
                    'siteid' => $site_id,
                    'key' => "product|color|" . str_replace(' ', '', substr(strtolower($color_key_replaced), 0, 10)),
                    'type' => 'color',
                    'domain' => 'product',
                    'code' => str_replace(' ', '', substr(strtolower($color_key_replaced), 0, 10)),
                    'label' => substr($label_replaced, 0, 31),
                    // 'pos' => 0,
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];

                DB::connection('cliqnshop')->table('mshop_attribute')->upsert(
                    $attribute,
                    ['unq_msatt_dom_type_code_sid'],
                    ['siteid', 'key', 'domain', 'code', 'label', 'type', 'mtime', 'editor']
                );
                $get_attribute = DB::connection('cliqnshop')->table('mshop_attribute')->where([
                    'code' => $attribute['code'], 'siteid' => $attribute['siteid'],
                    'type' => $attribute['type'], 'domain' => $attribute['domain']
                ])
                    ->pluck('id')->ToArray();
                $get_attribute_id = $get_attribute[0];
            }
            //length(mshop_attribute)
            $length_attribute = [];
            $get_attribute_id_length = '';
            if ($length_value != '') {
                $length_attribute = [
                    'siteid' => $site_id,
                    'key' => "product|length|" . round($length_value,2),
                    'type' => 'length',
                    'domain' => 'product',
                    'code' => round($length_value,2),
                    'label' => round($length_value,2) . '  ' . $length_unit,
                    // 'pos' => 0,
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];

                DB::connection('cliqnshop')->table('mshop_attribute')->upsert(
                    $length_attribute,
                    ['unq_msatt_dom_type_code_sid'],
                    ['siteid', 'key', 'code', 'label', 'type', 'domain', 'mtime', 'editor']
                );

                $get_attribute_length = DB::connection('cliqnshop')->table('mshop_attribute')
                    ->where([
                        'code' => $length_attribute['code'], 'domain' => $length_attribute['domain'], 'siteid' => $length_attribute['siteid'],
                        'type' => $length_attribute['type']
                    ])
                    ->pluck('id')->ToArray();
                $get_attribute_id_length = $get_attribute_length[0];
            }

            //width (mshop_attribute)
            $width_attribute = [];
            $get_attribute_id_width = '';
            if ($width_value != '') {

                $width_attribute = [
                    'siteid' => $site_id,
                    'key' => "product|width|" . round($width_value,2),
                    'type' => 'width',
                    'domain' => 'product',
                    'code' => round($width_value,2),
                    'label' => round($width_value,2) . '  ' . $width_unit,
                    // 'pos' => 0,
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];

                DB::connection('cliqnshop')->table('mshop_attribute')->upsert(
                    $width_attribute,
                    ['unq_msatt_dom_type_code_sid'],
                    ['siteid', 'key', 'code', 'type', 'domain', 'label', 'mtime',  'editor']
                );
                $get_attribute_width = DB::connection('cliqnshop')->table('mshop_attribute')
                    ->where([
                        'code' => $width_attribute['code'], 'domain' => $width_attribute['domain'], 'siteid' => $width_attribute['siteid'],
                        'type' => $width_attribute['type']
                    ])
                    ->pluck('id')->ToArray();
                $get_attribute_id_width = $get_attribute_width[0];
            }

            //price(mshop_price)
            $price = [];
            if ($Price_US_IN != '') {
                $price = [
                    'siteid' => $site_id,
                    'type' => 'default',
                    'domain' => 'product',
                    'label' => $currency_code . $Price_US_IN,
                    'currencyid' => $currency_code,
                    // 'quantity' => 1,
                    'value' => $Price_US_IN,
                    // 'costs' => 0.00,
                    // 'rebate' => 0.00,
                    'taxrate' => '{"tax":"19.00"}',
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];
                $id_price = DB::connection('cliqnshop')->table('mshop_price')->insertGetId($price);
            }

            //images(mshop_meadia)
            if (isset($image[$asin])) {

                $image_get_id = 0;
                foreach ($image[$asin] as $val) {
                    if ($val) {
                        $insert = $val;

                        $media = [
                            'siteid' => $site_id,
                            'type' => 'default',
                            'fsname' => 'product',
                            // 'langid' => NULL,
                            'domain' => 'product',
                            'label' => '',
                            'link' => $insert,
                            'preview' => "{\"1\": \"${insert}\"}",
                            'mimetype' => 'image/jpeg',
                            // 'status' => 1,
                            'mtime' => $date_time,
                            'ctime' => $date_time,
                            'editor' => 'App360',
                        ];

                        DB::connection('cliqnshop')->table('mshop_media')->updateOrInsert($media);
                        $image_get_id = DB::connection('cliqnshop')->table('mshop_media')->where('siteid', $media['siteid'])->where('mimetype', $media['mimetype'])->where('link', $media['link'])->select('id')->get();

                        $media_product_list = [
                            'siteid' => $site_id,
                            'parentid' => $get_product_id,
                            'key' => 'media|default|' . $image_get_id['0']->id,
                            'type' => 'default',
                            'domain' => 'media',
                            'refid' =>  $image_get_id['0']->id,
                            // 'start' => NULL,
                            // 'end' => NULL,
                            'config' => '[]',
                            // 'pos' => 0,
                            // 'status' => 1,
                            'mtime' => $date_time,
                            'ctime' => $date_time,
                            'editor' => 'App360',
                        ];

                        DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                            $media_product_list,
                            ['unq_msproli_pid_dm_ty_rid_sid'],
                            ['siteid', 'parentid', 'key', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                        );
                    }
                }
            }

            // $text_short = [
            //     'siteid' => $site_id,
            //     'type' => 'short',
            //     // 'langid' => NULL,
            //     'domain' => 'product',
            //     'label' => 'short description',
            //     'content' => $short_description,
            //     // 'status' => 1,
            //     'mtime' => $date_time,
            //     'ctime' => $date_time,
            //     'editor' => 'App360',
            // ];

            $text_long = [
                'siteid' => $site_id,
                'type' => 'long',
                // 'langid' => NULL,
                'domain' => 'product',
                'label' => 'long description',
                'content' => $long_description,
                // 'status' => 1,
                'mtime' => $date_time,
                'ctime' => $date_time,
                'editor' => 'App360',
            ];

            //id_text_short fetch
            // DB::connection('cliqnshop')->table('mshop_text')->updateOrInsert($text_short);
            // $id_short_text = DB::connection('cliqnshop')->table('mshop_text')->where('siteid', $text_short['siteid'])->where('content', $text_short['content'])
            //     ->pluck('id')->ToArray();
            // $id_text_short  = $id_short_text[0];

            //id_text_long fetch
            DB::connection('cliqnshop')->table('mshop_text')->updateOrInsert($text_long);
            $get_text_long = DB::connection('cliqnshop')->table('mshop_text')->where('siteid', $text_long['siteid'])->where('content', $text_long['content'])
                ->pluck('id')->ToArray();
            $get_text_long_id = $get_text_long[0];


            // category id Pluck(based on receved Category From CSV or Product search)        
            $catogory_data = DB::connection('cliqnshop')->table('mshop_catalog')->where('code', $category)->where('siteid', $site_id)->pluck('id')->ToArray();
            $catogory_id = DB::connection('cliqnshop')->table('mshop_catalog')->where('code', 'demo-new')->where('siteid', $site_id)->pluck('id')->ToArray();
            $catogory_id = $catogory_id[0];
            if (isset($catogory_data['0'])) {
                $catogory_id = $catogory_data['0'];
            }
            // category label Pluck(based on receved Category From CSV or Product search)
            $cat_label = DB::connection('cliqnshop')->table('mshop_catalog')->where('code', $category)->where('siteid', $site_id)->pluck('label')->ToArray();
            $catagory_label = '';
            if (isset($cat_label['0'])) {
                $catagory_label = $cat_label['0'];
            }

            //category insert to mshop_product_list
            $domain_catalog = [
                'siteid' => $site_id,
                'parentid' => $get_product_id,
                'key' => 'catalog|default|' . $catogory_id,  //query catalog_code with mshop_catalog anf get ID fill here(In place of 16)
                'type' => 'default',
                'domain' => 'catalog',
                'refid' =>  $catogory_id,
                // 'start' => NULL,
                // 'end' => NULL,
                'config' => '[]',
                'pos' => 0,
                // 'status' => 1,
                'mtime' => $date_time,
                'ctime' => $date_time,
                'editor' => 'App360',
            ];
            // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_catalog, [$domain_catalog['siteid'], $domain_catalog['parentid']]);
            DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                $domain_catalog,
                ['unq_msproli_pid_dm_ty_rid_sid'],
                ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'config', 'pos', 'mtime', 'editor']
            );

            //domain_supplier(brand) insert to mshop_product_list
            $domain_supplier = [];
            if (count($brand_insert) > 0 && ($get_brand_id) != '') {
                $domain_supplier = [
                    'siteid' => $site_id,
                    'parentid' => $get_product_id,
                    'key' => 'supplier|default|' . $get_brand_id,
                    'type' => 'default',
                    'domain' => 'supplier',
                    'refid' =>  $get_brand_id,
                    // 'start' => NULL,
                    // 'end' => NULL,
                    'config' => '[]',
                    'pos' => 0,
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];
                // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_supplier, [$domain_supplier['siteid'], $domain_supplier['parentid']]);
                DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                    $domain_supplier,
                    ['unq_msproli_pid_dm_ty_rid_sid'],
                    ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'config', 'pos', 'mtime', 'editor',]
                );
            }

            //mshop product_list  for Generic Keyword here removed to top

            if (!empty($attribute['label'])) {
                $domain_attribute = [
                    'siteid' => $site_id,
                    'parentid' => $get_product_id,
                    'key' => 'attribute|default|' . $get_attribute_id,
                    'type' => 'default',
                    'domain' => 'attribute',
                    'refid' => $get_attribute_id,
                    // 'start' => NULL,
                    // 'end' => NULL,
                    'config' => '[]',
                    // 'pos' => 0,
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];
                // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute, [$domain_attribute['siteid'], $domain_attribute['parentid']]);
                DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                    $domain_attribute,
                    ['unq_msproli_pid_dm_ty_rid_sid'],
                    ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'refid', 'config', 'mtime', 'editor']
                );
            }

            //domain_attribute(length) insert to mshop_product_list
            if (count($length_attribute) > 0 && isset($get_attribute_id_length)) {
                $domain_attribute_length = [
                    'siteid' => $site_id,
                    'parentid' => $get_product_id,
                    'key' => 'attribute|default|' . $get_attribute_id_length,
                    'type' => 'default',
                    'domain' => 'attribute',
                    'refid' => $get_attribute_id_length,
                    // 'start' => NULL,
                    // 'end' => NULL,
                    'config' => '[]',
                    // 'pos' => 0,
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];
                // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute_length, [$domain_attribute_length['siteid'], $domain_attribute_length['parentid']]);
                DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                    $domain_attribute_length,
                    ['unq_msproli_pid_dm_ty_rid_sid'],
                    ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'config', 'mtime', 'editor']
                );
            }

            //domain_attribute(width) insert to mshop_product_list
            if (count($width_attribute) > 0 && isset($get_attribute_id_width)) {
                $domain_attribute_width = [
                    'siteid' => $site_id,
                    'parentid' => $get_product_id,
                    'key' => 'attribute|default|' . $get_attribute_id_width,
                    'type' => 'default',
                    'domain' => 'attribute',
                    'refid' => $get_attribute_id_width,
                    // 'start' => NULL,
                    // 'end' => NULL,
                    'config' => '[]',
                    // 'pos' => 0,
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];


                // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute_width, [$domain_attribute_width['siteid'], $domain_attribute_width['parentid']]);
                DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                    $domain_attribute_width,
                    ['unq_msproli_pid_dm_ty_rid_sid'],
                    ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'config', 'mtime', 'editor']
                );
            }

            //domain_attribute(price) insert to mshop_product_list

            if (!empty($price) && isset($id_price)) {
                $domain_price = [
                    'siteid' => $site_id,
                    'parentid' => $get_product_id,
                    'key' => 'price|default|' . $id_price,
                    'type' => 'default',
                    'domain' => 'price',
                    'refid' => $id_price,
                    // 'start' => NULL,
                    // 'end' => NULL,
                    'config' => '[]',
                    // 'pos' => 0,
                    // 'status' => 1,
                    'mtime' => $date_time,
                    'ctime' => $date_time,
                    'editor' => 'App360',
                ];
                // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_price, [$domain_price['siteid'], $domain_price['parentid']]);
                DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                    $domain_price,
                    ['unq_msproli_pid_dm_ty_rid_sid'],
                    ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'config', 'mtime', 'editor']
                );
            }
            //domain_attribute(short Description) insert to mshop_product_list
            // $domain_text_short = [
            //     'siteid' => $site_id,
            //     'parentid' => $get_product_id,
            //     'key' => 'text|default|' . $id_text_short,
            //     'type' => 'default',
            //     'domain' => 'text',
            //     'refid' => $id_text_short,
            //     // 'start' => NULL,
            //     // 'end' => NULL,
            //     'config' => '[]',
            //     // 'pos' => 0,
            //     // 'status' => 1,
            //     'mtime' => $date_time,
            //     'ctime' => $date_time,
            //     'editor' => 'App360',
            // ];
            // // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_text_short, [$domain_text_short['siteid'], $domain_text_short['parentid']]);
            // DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
            //     $domain_text_short,
            //     ['unq_msproli_pid_dm_ty_rid_sid'],
            //     ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'config', 'mtime', 'ctime', 'editor']
            // );

            //domain_attribute(short Description) insert to mshop_product_list
            $domain_text_long = [
                'siteid' => $site_id,
                'parentid' => $get_product_id,
                'key' => 'text|default|' . $get_text_long_id,
                'type' => 'default',
                'domain' => 'text',
                'refid' => $get_text_long_id,
                // 'start' => NULL,
                // 'end' => NULL,
                'config' => '[]',
                // 'pos' => 0,
                // 'status' => 1,
                'mtime' => $date_time,
                'ctime' => $date_time,
                'editor' => 'App360',
            ];

            // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_text_long, [$domain_text_long['siteid'], $domain_text_long['parentid']]);
            DB::connection('cliqnshop')->table('mshop_product_list')->upsert(
                $domain_text_long,
                ['unq_msproli_pid_dm_ty_rid_sid'],
                ['siteid', 'parentid', 'key', 'refid', 'type', 'domain', 'config', 'mtime',  'editor']
            );

            //stock insert to(mshop_stock)
            $stock = [
                'siteid' => $site_id,
                'prodid' => $get_product_id,
                'type' => 'default',
                'stocklevel' => 500,
                // 'backdate' => NULL,
                // 'timeframe' => '',
                'mtime' => $date_time,
                'ctime' => $date_time,
                'editor' => 'App360',
            ];
            DB::connection('cliqnshop')->table('mshop_stock')->upsert(
                $stock,
                ['unq_mssto_pid_ty_sid'],
                ['siteid', 'prodid', 'type', 'stocklevel', 'mtime', 'editor']
            );

            //index (asin) to mshop_index_attribute

            if (!empty($attribute['label'])) {

                $index_attribute = [
                    'prodid' => $get_product_id,
                    'siteid' => $site_id,
                    'artid' => $get_product_id,
                    'attrid' => $get_attribute_id,
                    'listtype' => $domain_attribute['type'], // type from mshop_product_list
                    'type' => $attribute['type'],
                    'code' => $attribute['code'],
                    'mtime' => $date_time,
                ];
                DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert(
                    $index_attribute,
                    ['unq_msindat_p_s_aid_lt'],
                    ['prodid', 'siteid', 'artid', 'attrid', 'listtype', 'type', 'code', 'mtime']
                );
            }

            //index (length) to mshop_index_attribute
            if (!empty($length_attribute)) {
                $index_attribute_length = [
                    'prodid' => $get_product_id,
                    'siteid' => $site_id,
                    'artid' => $get_product_id,
                    'attrid' => $get_attribute_id,
                    'listtype' => $domain_attribute_length['type'], // type from mshop_product_list
                    'type' => $length_attribute['type'],
                    'code' => $length_attribute['code'],
                    'mtime' => $date_time,
                ];
                DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert(
                    $index_attribute_length,
                    ['unq_msindat_p_s_aid_lt'],
                    ['prodid', 'siteid', 'artid', 'attrid', 'listtype', 'type', 'code', 'mtime']
                );
            }

            // //mshop index for generic keyword  here replaced to top



            //index (width) to mshop_index_attribute
            if (!empty($width_attribute)) {

                $index_attribute_width = [
                    'prodid' => $get_product_id,
                    'siteid' => $site_id,
                    'artid' => $get_product_id,
                    'attrid' => $get_attribute_id,
                    'listtype' => $domain_attribute_width['type'], // type from mshop_product_list
                    'type' => $width_attribute['type'],
                    'code' => $width_attribute['code'],
                    'mtime' => $date_time,
                ];

                DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert(
                    $index_attribute_width,
                    ['unq_msindat_p_s_aid_lt'],
                    ['prodid', 'siteid', 'artid', 'attrid', 'listtype', 'type', 'code', 'mtime']
                );
            }

            //product_id to mshop_index_catalog
            $index_catalog = [
                'prodid' => $get_product_id,
                'siteid' => $site_id,
                'catid' => $domain_catalog['refid'],
                'listtype' => $domain_catalog['type'], // type from mshop_product_list
                'pos' => $domain_catalog['pos'], //from mshop_product_list
                'mtime' => $date_time,
            ];
            DB::connection('cliqnshop')->table('mshop_index_catalog')->upsert(
                $index_catalog,
                ['unq_msindca_p_s_cid_lt_po'],
                ['prodid', 'siteid', 'catid', 'listtype', 'pos', 'mtime']
            );


            //index_price to mshop_index_price
            if (count($price) > 0) {

                $index_price = [
                    'prodid' => $get_product_id,
                    'siteid' => $site_id,
                    'currencyid' => $price['currencyid'],
                    'value' => $price['value'],
                    'mtime' => $date_time,
                ];
                DB::connection('cliqnshop')->table('mshop_index_price')->upsert(
                    $index_price,
                    ['unq_msindpr_pid_sid_cid'],
                    ['prodid', 'siteid', 'currencyid', 'value', 'mtime']
                );
            }
            //domain_supplier to mshop Index Suplier
            if (count($domain_supplier) > 0) {
                $index_supplier = [
                    'prodid' => $get_product_id,
                    'siteid' => $site_id,
                    'supid' => $domain_supplier['refid'],
                    'listtype' => $domain_supplier['type'],
                    'latitude' => null,
                    'longitude' => null,
                    'pos' => $domain_supplier['pos'],
                    'mtime' => $date_time,
                ];

                DB::connection('cliqnshop')->table('mshop_index_supplier')->upsert(
                    $index_supplier,
                    ['unq_msindsu_p_s_lt_si_po_la_lo'],
                    ['prodid', 'siteid', 'supid', 'listtype', 'pos', 'mtime']
                );
            }

            //product_data to mshop_index_text
            $index_text = [
                'prodid' => $get_product_id,
                'siteid' => $site_id,
                'langid' => 'en',
                'url' => $product_data['url'],
                'name' => $product_data['label'],
                'content' => mb_strtolower($product_data['code']) . '<pre>' . mb_strtolower($product_data['label']) . '<pre>' . $keyword,
                'mtime' => $date_time,
            ];
            DB::connection('cliqnshop')->table('mshop_index_text')->upsert(
                $index_text,
                ['unq_msindte_pid_sid_lid_url'],
                ['prodid', 'siteid', 'url', 'name', 'content', 'mtime']
            );
        } catch (Exception $e) {
            Log::notice('Unmatched Data For' . $asin);
            Log::notice('CNS Insert' . $e);
        }
    }
}
