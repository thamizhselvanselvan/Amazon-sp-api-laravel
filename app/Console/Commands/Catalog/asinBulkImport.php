<?php

namespace App\Console\Commands\Catalog;

use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Console\Command;
use App\Models\Catalog\Asin_master;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
        $user_id = Auth::user()->id;
        Log::alert($user_id);

        $path = 'AsinMaster/asin.csv';
       
        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        $asin = [];
        $count = 0;
        foreach($csv as $key => $record)
        {
            $asin[] = [
                'asin' => $record['ASIN'],
                'user_id' => $user_id,
                'source' => $record['Source'],
                'destination_1' => $record['Destination_1'],
                'destination_2' => $record['Destination_2'],
                'destination_3' => $record['Destination_3'],
                'destination_4' => $record['Destination_4'],
                'destination_5' => $record['Destination_5']
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
