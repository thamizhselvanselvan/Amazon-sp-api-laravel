<?php

namespace App\Console\Commands;

use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class sellerAsinRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:seller-asin-remove {seller_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seller Asin Remove';

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
        $path = 'Seller/Remove/AsinMaster/remove_asin.csv';
        $seller_id = $this->argument('seller_id');
        // $seller_id = 1;

        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        Log::warning(" csv file importing");
        $stmt = (new Statement())
            ->where(function (array $record) {
                return $record;
            })
            ->offset(0)
            ;
           
        $records = $stmt->process($csv);

        $asin_master = [];
        $product = [];
        $product_lowest_price = [];
          

            $count = 0;
            foreach($records as $key => $record)
            {
                $asin = $record['ASIN'];
                $country_code = $record['Source'];

                $asin_master[] = [
                    'seller_id' => $seller_id,
                    'asin' => $asin,
                ];
                
                $product [] = [
                    'seller_id' => $seller_id,
                    'asin1' => $asin,
                    'country_code'=> $country_code,
                ];

                $product_lowest_price [] = [

                    'asin' => $asin,
                    'import_type' => 'Seller',
                    'country_code' => $country_code,
                ];

                if($count == 1000) {

                    // AsinMasterSeller::insert($asin_master);
                    // BB_Product::insert($product);
                    // BB_Product_lowest_price_offer::upsert($product_lowest_price, ['asin','country_code'],['asin','country_code']);
                    // $count = 0;
                    // $asin_master = [];
                    // $product = [];
                    // $product_lowest_price = [];
                }
                $count++;
                
            }	
            
            // AsinMasterSeller::insert($asin_master); 
            // BB_Product::insert($product);
            // BB_Product_lowest_price_offer::upsert($product_lowest_price, ['asin', 'country_code'], ['asin', 'country_code']);

            Log::warning(" asin import successfully");
    }
}
