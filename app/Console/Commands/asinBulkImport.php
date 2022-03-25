<?php

namespace App\Console\Commands;

use League\Csv\Reader;
use League\Csv\Statement;
use App\Models\Asin_master;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class asinBulkImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:asin-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Asin from CSV';

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
        Log::warning(" pms:asin-import command executed looking for path");

        $path = 'AsinMaster/asin.csv';
       
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
                    'asin' => $record['ASIN'],
                    'source' => $record['Source'],
                    'destination_1' => $record['Destination 1'],
                    'destination_2' => $record['Destination 2'],
                    'destination_3' => $record['Destination 3'],
                    'destination_4' => $record['Destination 4'],
                    'destination_5' => $record['Destination 5']
                ];
                if($count == 1000) {
                
                    Asin_master::upsert($asin, ['asin'], ['source','destination_1','destination_2','destination_3','destination_4','destination_5']);

                    $count = 0;
                    $asin = [];
                }
                $count++;
                
            }	
            
            Asin_master::upsert($asin, ['asin'], ['source','destination_1','destination_2','destination_3','destination_4','destination_5']); 
            Log::warning(" asin import successfully");
    }
}
