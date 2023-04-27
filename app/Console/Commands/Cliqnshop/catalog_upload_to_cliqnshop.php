<?php

namespace App\Console\Commands\Cliqnshop;

use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        $csv_body = array();


        $csv_data =  CSV_Reader($file_path);

        foreach ($csv_data as $data) {
            $asin[] = ($data['ASIN']);
        }

        $start_time = microtime(true);
        foreach (array_chunk($asin,100) as $a)  
        {
            $country = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $site_id)->select('code')->get();
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
                        ->join('pricing_uss', 'catalognewuss.asin', '=', 'pricing_uss.asin')
                        ->whereIn('catalognewuss.asin', $a)
                        ->get()->toArray();
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
            ->join('catalognewuss', 'catalognewins.asin', '=', 'catalognewuss.asin')
            ->join('pricing_ins', 'catalognewins.asin', '=', 'pricing_ins.asin')
            ->whereIn('catalognewins.asin', $a)
            ->get()->toArray();
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
            
            
            array_push($csv_body,$insertable_value);

        }
    }
        
                
        $csv_head =  ['site','category_code','asin','item_name','brand','brand_label','color_key','label','length_unit','length_value','width_unit','width_value','Price_US_IN','image','keyword','short_description','long_description','generic_keywords','status'];
        $this->csvExporter($csv_head ,$csv_body);


        // po($generic_keywords);
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        Log::info(" Execution time of cns product import from csv = ".$execution_time." sec");
    }

    public function csvExporter(Array $csv_head ,Array $csv_body)
    {
        // building/updating csv hader --start
            $valuesToReplace = ['image1','image2','image3','image4','image5','image6','image7','image8','image9','image10'];
            $csv_head = $this->ArrayContentReplacer(array:$csv_head , valuesToReplace:$valuesToReplace, target: 'image', isKey : false);            
        // building/updating csv hader --end 

        
        // building/updating csv body --start
            foreach ($csv_body as $key => $value) {                
                
                //  image array  builder  --start
                    $csvRow = $csv_body[$key];
                    $image =  $csvRow[13]; 
                    $asin =  $csvRow[2]; 
                    $imagesArray = $this->arrayStretch(array : $image[$asin] , totalElemens : 10 );
                    $csv_new_row = $this->ArrayContentReplacer(array:$csvRow , valuesToReplace:$imagesArray, target: 13, isKey : true);
                    $csv_body[$key] = $csv_new_row ;
                //  image array  builder  --end

                // generic keyword imploder --start
                    $csvRow = $csv_body[$key];
                    $generic_keyword_row_index = 26; 
                    $generic_keywords = $csvRow[$generic_keyword_row_index];
                    
                    $generic_keywords =  implode(",", array_map(function($item){
                        return implode ('', $item);
                    },$generic_keywords));   
                    
                    $generic_keywords_array = [ 0 => $generic_keywords];
                    $insertable_value = $this->ArrayContentReplacer(array:$csvRow , valuesToReplace:$generic_keywords_array, target: $generic_keyword_row_index, isKey : true);
                    $csv_body[$key] = $insertable_value ;
                // generic keyword imploder --end  
                
                //code to replace the siteid with sitecode data --start 
                    $site = $csv_body[$key][0]; 
                    $siteQry = DB::connection('cliqnshop')->table('mshop_locale_site')->select('code')->where('siteid', $site)->first();
                    $site = $siteQry->code;
                    $csv_body[$key][0] = $site;
                //code to replace the siteid with sitecode data --end

                    unset($csv_body[$key][27]); //removing editor coloumn
                 
            }
        // building/updating csv body --end
       
            $maxRowsForSigngleFile = 1000000 ;
            $offset = 1;       

            $csv_body_chunks =  array_chunk ($csv_body,$maxRowsForSigngleFile) ;
            foreach ($csv_body_chunks as $csv_body_chunk) 
            {
                $file_name = 'export_'.  date('Y-m-d_H-i-s') .'_file-'. $offset.".csv" ; 
                $exportFilePath = "Cliqnshop/upload/asin/export/" . $file_name;
                if (!Storage::exists($exportFilePath)) {
                    Storage::put($exportFilePath, '');
                }

                $csv_writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
                $csv_writer->insertOne($csv_head);
                $csv_writer->insertAll($csv_body_chunk);                
                $offset ++;
            }        
            
            
    }

    public function ArrayContentReplacer(Array $array, Array $valuesToReplace, $target ,bool $isKey = true) :Array
    {        
        if( $isKey)
        {   
            $index = $target;
            if ($index !== false) {
                array_splice($array, $index, 1, $valuesToReplace);
            }
            return $array;
        }
        else
        {
            $index = array_search($target, $array);
            if ($index !== false) {
                array_splice($array, $index, 1, $valuesToReplace);
            }
            return $array;
        }        
    }

    public function arrayStretch(Array $array , int $totalElemens ) :Array
    {        
        $filteredArray = array_values(array_filter($array)); 
        $newArray =  Array();
        for ($i=1; $i <= $totalElemens; $i++) 
        { 
            if(array_key_exists($i-1 , $filteredArray))
                $newArray[$i] = $filteredArray[$i-1];
            else
                $newArray[$i] = '';
        }
        
        return $newArray ;
    }

   
}
