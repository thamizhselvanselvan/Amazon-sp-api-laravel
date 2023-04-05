<?php

namespace App\Console\Commands\Cliqnshop;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Cliqnshop\CliqnshopCataloginsert;

class catalog_upload_to_cliqnshop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog_insert_cliqnshop {path}{site_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch ASIN Details From CatalogUSS table And Insert TO Cliqnsqhop Table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $site_id = $this->argument('site_id');
        $file_path = $this->argument('path');
        $editor = 'cns_search';


        $csv_data =  CSV_Reader($file_path);

        foreach ($csv_data as $data) {
            $asin[] = ($data['ASIN']);
        }

        $headers = [
            'catalognewuss.asin',
            'catalognewuss.brand',
            'catalognewuss.images',
            'catalognewuss.item_name',
            'catalognewuss.browse_classification',
            'catalognewuss.dimensions',
            'catalognewuss.attributes',
            'catalognewuss.color',
            'pricing_uss.usa_to_in_b2c',
            'pricing_uss.us_price',
            'pricing_uss.usa_to_uae',

        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');
        $result = $table_name->select($headers)
            ->join('pricing_uss', 'catalognewuss.asin', '=', 'pricing_uss.asin')
            ->whereIn('catalognewuss.asin', $asin)
            ->get()->toArray();

        $generic_keywords = [];


        foreach ($result as $data) {

            $img1 = [
                "Images1" => '',
                "Images2" => '',
                "Images3" => '',
                "Images4" => '',
                "Images5" => '',
                "Images6" => '',
                "Images7" => '',
                "Images8" => '',
                "Images9" => '',
                "Images10" => '',
            ];

            $imagedata = json_decode($data['images'], true);
            //old image logic
            // if (isset($imagedata[0]['images'])) {

            //     foreach ($imagedata[0]['images'] as $counter => $image_data_new) {
            //         $counter++;
            //         if (array_key_exists("link", $image_data_new)) {

            //             if ($img1["Images${counter}"] = $image_data_new['height'] == 75) {

            //                 $img1["Images${counter}"] = '';
            //             } else  if ($img1["Images${counter}"] = $image_data_new['height'] == 500) {
            //                 $img1["Images${counter}"] = $image_data_new['link'];
            //             }
            //         } else {
            //             $img1["Images${counter}"] = '';
            //         }
            //         if ($counter == 10) {
            //             break;
            //         }
            //     }
            // } else {
            //     for ($i = 1; $i <= 5; $i++) {
            //         $img1["Images${i}"] = '';
            //     }
            // }
            // $image[$data['asin']] = $img1;
            if (isset($imagedata[0]['images'])) {

                foreach ($imagedata[0]['images'] as $counter => $image_data_new) {
                    $counter++;

                    if (array_key_exists("link", $image_data_new)) {
                        $img1["Images${counter}"] = '';
                        if ($counter == 1) {
                            if ($image_data_new['height'] > 500 || $image_data_new['width'] > 500) {
                            ($img1["Images${counter}"] = $image_data_new['link']);
                            }
                        } else if ($counter == 4) {
                            if ($image_data_new['height'] > 500 || $image_data_new['width'] > 500) {
                                ($img1["Images${counter}"] = $image_data_new['link']);
                                }
                        } else if ($counter == 7) {
                            if ($image_data_new['height'] > 500 || $image_data_new['width'] > 500) {
                                ($img1["Images${counter}"] = $image_data_new['link']);
                                }
                        } else if ($counter == 10) {
                            if ($image_data_new['height'] > 500 || $image_data_new['width'] > 500) {
                                ($img1["Images${counter}"] = $image_data_new['link']);
                                }
                        } else if ($counter == 13) {
                            if ($image_data_new['height'] > 500 || $image_data_new['width'] > 500) {
                                ($img1["Images${counter}"] = $image_data_new['link']);
                                }
                        }
                        else if ($counter == 16) {
                            if ($image_data_new['height'] > 500 || $image_data_new['width'] > 500) {
                                ($img1["Images${counter}"] = $image_data_new['link']);
                                }
                        }
                        else if ($counter == 19) {
                            if ($image_data_new['height'] > 500 || $image_data_new['width'] > 500) {
                                ($img1["Images${counter}"] = $image_data_new['link']);
                                }
                        }
                    }
                }
            } else {
                for ($i = 1; $i <= 7; $i++) {
                    $img1["Images${i}"] = '';
                }
            }

            $image[$data['asin']] = ($img1);
            $long_description = '';
            $short_description = '';

            if (isset($data['attributes'])) {

                $desc = json_decode($data['attributes'], true);
                if (isset($desc['bullet_point']) && !empty($desc['bullet_point'])) {

                    $bullet = $desc['bullet_point'];
                    foreach ($bullet as $key => $val) {

                        $short_description = $val['value'];
                        $long_description .=  '<p>' . $val['value'];
                    }
                }
            }

            $asin =  $data['asin'];

            $item_name = $data['item_name'];
            $item_url = str_replace(' ', '-', $data['item_name']);
            $url = (strtolower($item_url));

            $country = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $site_id)->select('code')->get();

            $Price_US_IN = $data['usa_to_in_b2c'];
            if (isset($country['0']->code)) {
                if (($country['0']->code) == 'in') {
                    $Price_US_IN = $data['usa_to_in_b2c'];
                } else if ($country['0']->code == 'uae') {
                    $Price_US_IN  = $data['usa_to_uae'];
                }
            }


            $catalog_code = json_decode($data['browse_classification'], true);
            $category_code = 'demo-new';

            if ($catalog_code == null) {
                $category_code = 'demo-new';
            } else if (isset($catalog_code['classificationId'])) {
                $category_code = $catalog_code['classificationId'];
            }

            $brand_label = ' ';
            if ($data['brand']) {

                $brand_label = $data['brand'];
            }
            $brand_place = str_replace(' ', '', $data['brand']);
            $brand =  substr(strtolower($brand_place), 0, 10);

            $color_code = '';
            $color_label = '';
            $label = '';
            $color_key = '';
            if (isset($data['color'])) {
                $color_code = str_replace(' ', '', $data['color']);
                $color_label = $data['color'];
                $label =  ucfirst($color_label);
                $color_key = str_replace(' ', '', substr(strtolower($label), 0, 10));
            }

            //dimensions Fetch
            $length_unit = '';
            $length_value = '';
            $width_unit = '';
            $width_value  = '';
            if (isset($data['dimensions'])) {

                $length_unit  = '';
                $length_value = '';
                $dim = json_decode($data['dimensions'], true);
                if (isset($dim[0]['item']['length'])) {
                    $length_unit  = $dim[0]['item']['length']['unit'];
                    $length_value  = round($dim[0]['item']['length']['value'], 3);
                }

                $width_unit  = '';
                $width_value = '';
                if (isset($dim[0]['item']['width'])) {
                    $width_unit  = $dim[0]['item']['width']['unit'];
                    $width_value  = round($dim[0]['item']['width']['value'], 3);
                }
            }
            //genric Keywords

            $gener_key = [];
            $generic_keywords = [];
            if (isset($data['attributes'])) {

                $genric_key = json_decode($data['attributes'], true);

                if (isset($genric_key['generic_keyword']) && !empty($genric_key['generic_keyword'])) {

                    $generic_array = $genric_key['generic_keyword'];

                    foreach ($generic_array as $key => $val) {

                        // $gener_key[] = explode(",", $val['value']);
                        $gener_key[] = preg_split("/[,;]/", $val['value']);
                    }

                    $generic_keywords = $gener_key;
                }
            }


            // if ($category[$asin] == '') {
            //     $category_code = 'demo-new';
            // } else {

            //     $category_code = $category[$asin];
            // }

            $keyword = 'csv_bulk_upload';
            $insert_service = new CliqnshopCataloginsert();
            $insert_service->insertdata_cliqnshop(
                $site_id,
                $category_code,
                $asin,
                $item_name,
                $brand,
                $brand_label,
                $color_key,
                $label,
                $length_unit,
                $length_value,
                $width_unit,
                $width_value,
                $Price_US_IN,
                $image,
                $keyword,
                $short_description,
                $long_description,
                $generic_keywords,
                $keyword
            );
        }

        // po($generic_keywords);


    }
}
