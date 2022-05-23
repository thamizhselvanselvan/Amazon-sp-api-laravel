<?php

namespace App\Console\Commands;

use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\seller\AsinMasterSeller;
use Illuminate\Support\Facades\Storage;

class seller_asin_import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:seller-asin-import {seller_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $path = 'Seller/AsinMaster/asin.csv';
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

        $asin = [];
          

            $count = 0;
            foreach($records as $key => $record)
            {
                $asin[] = [
                    'seller_id' => $seller_id,
                    'asin' => $record['ASIN'],
                    'source' => $record['Source'],
                    'destination_1' => $record['Destination 1'],
                    'destination_2' => $record['Destination 2'],
                    'destination_3' => $record['Destination 3'],
                    'destination_4' => $record['Destination 4'],
                    'destination_5' => $record['Destination 5'],         
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                if($count == 1000) {
                    AsinMasterSeller::insert($asin);
                    $count = 0;
                    $asin = [];
                }
                $count++;
                
            }	
            
            AsinMasterSeller::insert($asin); 
            Log::warning(" asin import successfully");
    }
}
