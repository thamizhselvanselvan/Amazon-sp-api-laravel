<?php

namespace App\Console\Commands\Cliqnshop;

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\Cliqnshop\CliqnshopCataloginsert;
use App\Services\Cliqnshop\CSV_Exporter;

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
    protected $description = 'Fetch ASIN Details From catalognewuss/catalognewins table And Insert TO Cliqnsqhop Table';

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
        $start_time = microtime(true);

        $site_id = $this->argument('site_id');
        $file_path = $this->argument('path');
        $csvExporter = new CSV_Exporter();


        $csv_data =  CSV_Reader($file_path);

        foreach ($csv_data as $data) {
            $asin[] = ($data['ASIN']);
        }

        $country = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $site_id)->select('code')->get();
       
        $current_time = Carbon::now()->toDateTimeString();
        $time =  str_replace(array(':', ' '), array('-', '_'), $current_time);
        if (isset($country['0']->code)) {
            if (($country['0']->code) == 'in') {
                //Error file
                $filename = "public/Cliqnshop/IN/Asin_Error_Report_IN_$time.txt";
                if (!Storage::exists($filename)) {
                    Storage::put($filename, '');
                }
                $exportFilePath = $filename;
                $path = Storage::disk('local')->path($filename);
                $file = fopen($path, 'w');

                //Success file
                $filename_s = "public/Cliqnshop/IN/Asin_Success_Report_IN_$time.txt";
                if (!Storage::exists($filename_s)) {
                    Storage::put($filename_s, '');
                }
                $exportFilePath_s = $filename_s;
                $path_s = Storage::disk('local')->path($filename_s);
                $file_s = fopen($path_s, 'w');
            }
        }

        if (isset($country['0']->code)) {
        if (($country['0']->code) == 'uae') {
               //Error file
               $filename = "public/Cliqnshop/UAE/Asin_Error_Report_UAE_$time.txt";
               if (!Storage::exists($filename)) {
                   Storage::put($filename, '');
               }
               $exportFilePath = $filename;
               $path = Storage::disk('local')->path($filename);
               $file = fopen($path, 'w');

               //Success file
               $filename_s = "public/Cliqnshop/UAE/Asin_Success_Report_UAE_$time.txt";
               if (!Storage::exists($filename_s)) {
                   Storage::put($filename_s, '');
               }
               $exportFilePath_s = $filename_s;
               $path_s = Storage::disk('local')->path($filename_s);
               $file_s = fopen($path_s, 'w');
            }
        }
        foreach (array_chunk($asin,100) as $a)  
        {
            
            if (isset($country['0']->code)) {
                if (($country['0']->code) == 'in') {
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
            ->leftJoin('pricing_uss', 'catalognewuss.asin', '=', 'pricing_uss.asin')
            ->whereIn('catalognewuss.asin', $a)
            ->get()->toArray();

            $check_result = $table_name->select($headers)
            ->leftJoin('pricing_uss', 'catalognewuss.asin', '=', 'pricing_uss.asin')
            ->whereIn('catalognewuss.asin', $a)
            ->pluck('asin')->toArray();

            $not_founds = array_diff($a, $check_result);
                
            foreach ($not_founds as $not_found)
            {
                fwrite($file, 'Asin '. $not_found . ' Not Found' .  "\n");
            }
    }
}

            if (($country['0']->code) == 'uae') {
                    if (isset($country['0']->code)) {
            $headers = [
            'catalognewins.asin',
            'catalognewins.brand',
            'catalognewins.images',
            'catalognewins.item_name',
            'catalognewuss.browse_classification',
            'catalognewins.dimensions',
            'catalognewins.attributes',
            'catalognewins.color',
            // 'pricing_ins.usa_to_in_b2c',
            // 'pricing_ins.us_price',
            'pricing_ins.ind_to_uae',
            
            ];

            
            $table_name = table_model_create(country_code: 'in', model: 'Catalog', table_name: 'catalognew');
            $result = $table_name->select($headers)
            ->leftJoin('catalognewuss', 'catalognewins.asin', '=', 'catalognewuss.asin')
            ->leftJoin('pricing_ins', 'catalognewins.asin', '=', 'pricing_ins.asin')
            ->whereIn('catalognewins.asin', $a)
            ->get()->toArray();

            $check_result = $table_name->select($headers)
            ->leftJoin('catalognewuss', 'catalognewins.asin', '=', 'catalognewuss.asin')
            ->leftJoin('pricing_ins', 'catalognewins.asin', '=', 'pricing_ins.asin')
            ->whereIn('catalognewins.asin', $a)
            ->pluck('asin')->toArray();


            $not_founds = array_diff($a, $check_result);
                
            foreach ($not_founds as $not_found)
            {
                fwrite($file, 'Asin '. $not_found . ' Not Found' .  "\n");
            }
            }
            }

        $generic_keywords = [];


        foreach ($result as $data) {

            // $Price_US_IN = $data['usa_to_in_b2c'];
            $Price_US_IN = [];
            if (isset($country['0']->code)) {
                if (($country['0']->code) == 'in') {
                    $Price_US_IN = $data['usa_to_in_b2c'];
                } else if ($country['0']->code == 'uae') {
                    $Price_US_IN  = $data['ind_to_uae'];
                }
            }

           
            $catalog_code = json_decode($data['browse_classification'], true);
            $category_code = 'demo-new';

            if ($catalog_code == null) {
                $category_code = 'demo-new';
            } else if (isset($catalog_code['classificationId'])) {
                $category_code = $catalog_code['classificationId'];
            }
       
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
            $editor = 'csv_bulk';
            $keyword = '';
            $display_code  = 1;
            $insert_service = new CliqnshopCataloginsert();
            if ($category_code == 'demo-new')
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Category not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Category Not Found. So Imported in New Arrival Category'. "\n");
            }
            else {
                $catogory_data = DB::connection('cliqnshop')->table('mshop_catalog')->where('code', $category_code)->where('siteid', $site_id)->pluck('id')->ToArray();
                if (!isset($catogory_data[0]))
                {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Category Not Found in Cliqnshop Database or Incorrect Category Found From Catalog Database'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Category Not Found in Cliqnshop Database or Incorrect Category Found From Catalog Database. So Imported in New Arrival Category'. "\n");
                }
            }
            if ($item_name == '')
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Product Name not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Product Name not found, So Imported with Disable Status'. "\n"); 
            }
            if ($brand_label == ' ')
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Brand not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Imported without Brand'. "\n");
            }
            if ($label == '')
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Colour not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Imported without Colour'. "\n");
            }
            if ($length_unit  == '' && $length_value == '')
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Length not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Imported without Length'. "\n");
            }
            if ($width_unit  == '' && $width_value == '')
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Width not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Imported without Width'. "\n");
            }
            if ($Price_US_IN == [] || $Price_US_IN == '0' || $Price_US_IN == '')
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Price not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Price not found, So Imported with Disable Status'. "\n");
            }
            $required = ['Images1','Images2','Images3','Images4','Images5','Images6','Images7','Images8','Images9','Images10'];
            if (count(array_intersect_key(array_flip($required), $image[$asin])) === count($required)) {
                if ($image[$asin]['Images1'] == '' && 
             $image[$asin]['Images2'] == '' && 
             $image[$asin]['Images3'] == '' && 
             $image[$asin]['Images4'] == '' && 
             $image[$asin]['Images5'] == '' && 
             $image[$asin]['Images6'] == '' && 
             $image[$asin]['Images7'] == '' && 
             $image[$asin]['Images8'] == '' && 
             $image[$asin]['Images9'] == '' && 
             $image[$asin]['Images10'] == '')
             {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Image not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Image not found, So Imported with Disable Status'. "\n");
             }
            }
            if ($long_description == '')
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Description not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Imported without Description'. "\n");
            }
            if ($generic_keywords == '' || $generic_keywords == [])
            {
                fwrite($file, 'Asin'.' '. $asin . ' - '. 'Generic Keywords not found'. "\n");
                fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Imported without Generic Keywords'. "\n");
            }
           
            $insertable_value = [
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
                $editor,
                $display_code
            ];

            $insert_service->insertdata_cliqnshop(
               ...$insertable_value
            );

            $csvExporter->set_csv_body($insertable_value);   

            if ($item_name !== '')
            {
             if ($brand_label !== ' ')
             {
                 if ($generic_keywords !== '' || $generic_keywords !== [])
                 {
                     if ($label !== '')
                     {
                         if ($length_unit  !== '' && $length_value !== '')
                         {
                             if ($width_unit  !== '' && $width_value !== '')
                             {
                                if ($Price_US_IN !== [] || $Price_US_IN !== '0' || $Price_US_IN !== '')
                                {
                                 if ($long_description !== '')
                                 {
                                     if ($category_code !== 'demo-new')
                                     {
                                         $catogory_data = DB::connection('cliqnshop')->table('mshop_catalog')->where('code', $category_code)->where('siteid', $site_id)->pluck('id')->ToArray();
                                         if (isset($catogory_data[0]))
                                         {
                                 
                                             if ($image[$asin]['Images1'] !== '' ||
                                          $image[$asin]['Images2'] !== '' || 
                                          $image[$asin]['Images3'] !== '' || 
                                          $image[$asin]['Images4'] !== '' || 
                                          $image[$asin]['Images5'] !== '' || 
                                          $image[$asin]['Images6'] !== '' || 
                                          $image[$asin]['Images7'] !== '' || 
                                          $image[$asin]['Images8'] !== '' || 
                                          $image[$asin]['Images9'] !== '' || 
                                          $image[$asin]['Images10'] !== '')
                                          {
                                         fwrite($file_s, 'Asin'.' '. $asin . ' - '. 'Successfully Imported'. "\n");
                                          }
                                         }
                                     }
                                 }
                                }
                             }
                         }
                     }
                 }
             }
            }

        }
    }
               fclose($file);
               fclose($file_s);

                $url = Storage::url($exportFilePath);

                $slackMessage = config('app.url').$url;

                $url_s = Storage::url($exportFilePath_s);

                $slackMessage_s = config('app.url').$url_s;

                slack_notification('aimeos', 'Product Import From CSV Error Report testing', $slackMessage);
                slack_notification('aimeos', 'Product Import From CSV Sucsess Report testing', $slackMessage_s);

                $csv_head =  ['site','category_code','asin','item_name','brand_code','brand_label','color_code','color_label','length_unit','length_value','width_unit','width_value','price','image','long_description','generic_keywords'];

                $csvExporter->set_csv_head($csv_head);
                $csvExporter->set_file_path('Cliqnshop/upload/asin/export/');
                $csvExporter->set_file_name_prefix('csv_export_');
                $csvExporter->generate();
                
              // po($generic_keywords);
                $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        Log::info(" Execution time of cns product import from csv = ".$execution_time." sec");
        
        }


   
}
