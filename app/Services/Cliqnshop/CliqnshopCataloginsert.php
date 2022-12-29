<?php

namespace App\Services\Cliqnshop;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CliqnshopCataloginsert
{
    public function insertdata_cliqnshop($asin,  $item_name,  $brand,  $brand_label,  $color_key,  $label,  $length_unit,  $length_value,  $width_unit,  $width_value,  $Price_US_IN,  $image,  $short_description,  $long_description)
    {

        $date_time = Carbon::now();
        $product_data = [
            'siteid' => '1.',
            // 'dataset' => '',
            'type' => 'default',
            'code' => $asin, //ASIN
            'label' => $item_name,
            'url' => $asin,
            'config' => '[]',
            // 'start' => NULL,
            // 'end' => NULL,
            'scale' => 1,
            // 'rating' => 0.00,
            // 'ratings' => 0,
            'instock' => 1,
            // 'target' => '',
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];
        DB::connection('cliqnshop')->table('mshop_product')->upsert($product_data, ['siteid', 'code']);

        $get_product = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $product_data['siteid'])->where('code', $product_data['code'])
            ->pluck('id')->ToArray();
        $get_product_id = $get_product[0];

        $category = [];

        $brand_insert = [
            'siteid' => '1.',
            'code' =>  $brand,
            'label' =>  $brand_label,
            // 'status' => 1,
            // 'pos' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_supplier')->upsert($brand_insert, ['siteid', 'code']);
        $get_brand = DB::connection('cliqnshop')->table('mshop_supplier')->where('siteid', $brand_insert['siteid'])->where('code', $brand_insert['code'])
            ->pluck('id')->ToArray();
        $get_brand_id = $get_brand[0];

        $attribute = [
            'siteid' => '1.',
            'key' => "product|color|" . $color_key,
            'type' => 'color',
            'domain' => 'product',
            'code' => $color_key,
            'label' => $label,
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_attribute')->upsert($attribute, ['siteid', 'code']);
        $get_attribute = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $attribute['siteid'])->where('code', $attribute['code'])
            ->pluck('id')->ToArray();
        $get_attribute_id = $get_attribute[0];


        $length_attribute = [
            'siteid' => '1.',
            'key' => "product|length|" . $length_value,
            'type' => 'length',
            'domain' => 'product',
            'code' => $length_value,
            'label' => $length_value . '  ' . $length_unit,
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_attribute')->upsert($length_attribute, ['siteid', 'code']);

        $get_attribute_length = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $length_attribute['siteid'])->where('code', $length_attribute['code'])
            ->pluck('id')->ToArray();
        $get_attribute_id_length = $get_attribute_length[0];

        $width_attribute = [
            'siteid' => '1.',
            'key' => "product|width|" . $width_value,
            'type' => 'width',
            'domain' => 'product',
            'code' => $width_value,
            'label' => $width_value . '  ' . $width_unit,
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_attribute')->upsert($width_attribute, ['siteid', 'code']);
        $get_attribute_width = DB::connection('cliqnshop')->table('mshop_attribute')->where('siteid', $width_attribute['siteid'])->where('code', $width_attribute['code'])
            ->pluck('id')->ToArray();
        $get_attribute_id_width = $get_attribute_width[0];




        $price = [
            'siteid' => '1.',
            'type' => 'default',
            'domain' => 'product',
            'label' => 'INR' . $Price_US_IN,
            'currencyid' => 'INR',
            // 'quantity' => 1,
            'value' => $Price_US_IN,
            // 'costs' => 0.00,
            // 'rebate' => 0.00,
            'taxrate' => '{"tax":"19.00"}',
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        $id_price = DB::connection('cliqnshop')->table('mshop_price')->insertGetId($price);

        
        if (isset($image[$asin])) {
            
            $image_get_id = 0;
            foreach ($image[$asin] as $val) {
                if ($val) {
                    $insert = $val;

                    $media = [
                        'siteid' => '1.',
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
                        'editor' => 'test',
                    ];

                     DB::connection('cliqnshop')->table('mshop_media')->updateOrInsert($media);
                    $image_get_id = DB::connection('cliqnshop')->table('mshop_media')->where('link', $media['link'])->select('id')->get();

                    $media_product_list = [
                        'siteid' => '1.',
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
                        'editor' => 'test',
                    ];

                    DB::connection('cliqnshop')->table('mshop_product_list')->upsert($media_product_list, ['siteid', 'parentid']);
                 
                }
            }
        }


        $text_short = [
            'siteid' => '1.',
            'type' => 'short',
            // 'langid' => NULL,
            'domain' => 'product',
            'label' => 'short description',
            'content' => (json_encode($short_description)),
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        $text_long = [
            'siteid' => '1.',
            'type' => 'long',
            // 'langid' => NULL,
            'domain' => 'product',
            'label' => 'long description',
            'content' => json_encode(($long_description)),
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        $id_text_short = DB::connection('cliqnshop')->table('mshop_text')->insertGetId($text_short);
        DB::connection('cliqnshop')->table('mshop_text')->updateOrInsert($text_long);
        $get_text_long = DB::connection('cliqnshop')->table('mshop_text')->where('siteid', $text_long['siteid'])->where('content', $text_long['content'])
            ->pluck('id')->ToArray();
        $get_text_long_id = $get_text_long[0];

        $domain_catalog = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'catalog|default|16',  //query catalog_code with mshop_catalog anf get ID fill here(In place of 16)
            'type' => 'default',
            'domain' => 'catalog',
            'refid' => 16,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];
        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_catalog, [$domain_catalog['siteid'], $domain_catalog['parentid']]);
        $domain_supplier = [
            'siteid' => '1.',
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
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_supplier, [$domain_supplier['siteid'], $domain_supplier['parentid']]);
        $domain_attribute = [
            'siteid' => '1.',
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
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute, [$domain_attribute['siteid'], $domain_attribute['parentid']]);
        $domain_attribute_length = [
            'siteid' => '1.',
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
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute_length, [$domain_attribute_length['siteid'], $domain_attribute_length['parentid']]);
        $domain_attribute_width = [
            'siteid' => '1.',
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
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_attribute_width, [$domain_attribute_width['siteid'], $domain_attribute_width['parentid']]);

        $domain_price = [
            'siteid' => '1.',
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
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_price, [$domain_price['siteid'], $domain_price['parentid']]);
        // $domain_media = [
        //     'siteid' => '1.',
        //     'parentid' => $get_product_id,
        //     'key' => 'media|default|' . $$image_get_id,
        //     'type' => 'default',
        //     'domain' => 'media',
        //     'refid' => $ $image_get_id,
        //     // 'start' => NULL,
        //     // 'end' => NULL,
        //     'config' => '[]',
        //     // 'pos' => 0,
        //     // 'status' => 1,
        //     'mtime' => $date_time,
        //     'ctime' => $date_time,
        //     'editor' => 'test',
        // ];

        // DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_media, [$domain_media['siteid'], $domain_media['parentid']]);
        $domain_text_short = [
            'siteid' => '1.',
            'parentid' => $get_product_id,
            'key' => 'text|default|' . $id_text_short,
            'type' => 'default',
            'domain' => 'text',
            'refid' => $id_text_short,
            // 'start' => NULL,
            // 'end' => NULL,
            'config' => '[]',
            // 'pos' => 0,
            // 'status' => 1,
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_text_short, [$domain_text_short['siteid'], $domain_text_short['parentid']]);
        $domain_text_long = [
            'siteid' => '1.',
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
            'editor' => 'test',
        ];

        DB::connection('cliqnshop')->table('mshop_product_list')->upsert($domain_text_long, [$domain_text_long['siteid'], $domain_text_long['parentid']]);

        $stock = [
            'siteid' => '1.',
            'prodid' => $get_product_id,
            'type' => 'default',
            'stocklevel' => 500,
            // 'backdate' => NULL,
            // 'timeframe' => '',
            'mtime' => $date_time,
            'ctime' => $date_time,
            'editor' => 'test',
        ];
        DB::connection('cliqnshop')->table('mshop_stock')->upsert($stock, ['siteid', 'prodid']);

        //index tables
        $index_attribute = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'artid' => $get_product_id,
            'attrid' => $get_attribute_id,
            'listtype' => $domain_attribute['type'], // type from mshop_product_list
            'type' => $attribute['type'],
            'code' => $attribute['code'],
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert($index_attribute, ['siteid', 'prodid']);

        $index_attribute_length = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'artid' => $get_product_id,
            'attrid' => $get_attribute_id,
            'listtype' => $domain_attribute_length['type'], // type from mshop_product_list
            'type' => $length_attribute['type'],
            'code' => $length_attribute['code'],
            'mtime' => $date_time,
        ];
        DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert($index_attribute_length, ['siteid', 'prodid']);
        $index_attribute_width = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'artid' => $get_product_id,
            'attrid' => $get_attribute_id,
            'listtype' => $domain_attribute_width['type'], // type from mshop_product_list
            'type' => $width_attribute['type'],
            'code' => $width_attribute['code'],
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert($index_attribute_width, ['siteid', 'prodid']);

        $index_catalog = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'catid' => $domain_catalog['refid'],
            'listtype' => $domain_catalog['type'], // type from mshop_product_list
            'pos' => $domain_catalog['pos'], //from mshop_product_list
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_catalog')->upsert($index_catalog, ['siteid', 'prodid']);
        $index_price = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'currencyid' => $price['currencyid'],
            'value' => $price['value'],
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_price')->upsert($index_price, ['siteid', 'prodid']);
        $index_supplier = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'supid' => $domain_supplier['refid'],
            'listtype' => $domain_supplier['type'],
            'latitude' => null,
            'longitude' => null,
            'pos' => $domain_supplier['pos'],
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_supplier')->upsert($index_supplier, ['siteid', 'prodid']);

        $index_supplier = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'supid' => $domain_supplier['refid'],
            'listtype' => $domain_supplier['type'],
            'latitude' => null,
            'longitude' => null,
            'pos' => $domain_supplier['pos'],
            'mtime' => $date_time,
        ];

        DB::connection('cliqnshop')->table('mshop_index_supplier')->upsert($index_supplier, ['siteid', 'prodid']);


        $index_text = [
            'prodid' => $get_product_id,
            'siteid' => '1.',
            'langid' => 'en',
            'url' => $product_data['url'],
            'name' => $product_data['label'],
            'content' => $product_data['code'] . '<pre>' . $product_data['label'] .
                '<pre>' . 'New arrivals' . '<pre>' . $brand_insert['label'] . '<pre>' . $attribute['label'] . '<pre>'
                . $text_short['content'] . '<pre>' . $text_long['content'],
            'mtime' => $date_time,
        ]; //category hardcoded

        DB::connection('cliqnshop')->table('mshop_index_text')->upsert($index_text, [$index_text['siteid'], $index_text['prodid']]);
        // $product_list = [
        //     'siteid' => '1.',
        //     'parentid' => $table_1,
        //     'key' =>
        // ]
    }
}