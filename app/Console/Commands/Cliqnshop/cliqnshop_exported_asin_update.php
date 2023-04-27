<?php

namespace App\Console\Commands\Cliqnshop;

use League\Csv\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Cliqnshop\CliqnshopCataloginsert;

class cliqnshop_exported_asin_update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:cliqnshop_exported_asin_update {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command is used to update the  asins of cliqnshop when modified csv is imported';

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

        $file_path = $this->argument('path');
        $csv_data =  CSV_Reader($file_path);

        

        foreach ($csv_data as $data) 
        {
            $site_id = $data['site'] ;
            $siteQry = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid')->where('code', $site_id)->first();
            $site_id = $siteQry->siteid;
            
            $category_code = $data['category_code'];
            $asin = $data['asin'];
            $item_name = $data['item_name'];
            $brand = $data['brand'];
            $brand_label = $data['brand_label'];
            $color_key = $data['color_key'];
            $label = $data['label'];
            $length_unit = $data['length_unit'];
            $length_value = $data['length_value'];
            $width_unit = $data['width_unit'];
            $width_value = $data['width_value'];
            $Price_US_IN = $data['Price_US_IN'];

            $image = Array(
                            $data['asin'] => [
                                                'Images1' => $data['image1'],
                                                'Images2' => $data['image2'],
                                                'Images3' => $data['image3'],
                                                'Images4' => $data['image4'],
                                                'Images5' => $data['image5'],
                                                'Images6' => $data['image6'],
                                                'Images7' => $data['image7'],
                                                'Images8' => $data['image8'],
                                                'Images9' => $data['image9'],
                                                'Images10' => $data['image10']
                                            ]
                            
                        );

            $short_description = $data['short_description'];
            $long_description = $data['long_description'];

            $generic_keywords_exploded = explode (",", $data['generic_keywords']); 
            $generic_keywords = Array();            
            foreach ($generic_keywords_exploded as $key => $value) {
                $generic_keywords [] = Array(
                    $value
                );
            }
            
            
            $editor = 'csv_bulk';
            $keyword = '';
            $display_code  = 1;
            $insert_service = new CliqnshopCataloginsert();

            $insertable_values = [
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
            ...$insertable_values
            );

        }

        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        Log::info(" Execution time of cns product update or insert  from csv file = ".$execution_time." sec");

        
        return 1;
    }
}
