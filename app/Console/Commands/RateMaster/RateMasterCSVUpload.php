<?php

namespace App\Console\Commands\RateMaster;

use config;
use RedBeanPHP\R;
use League\Csv\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RateMasterCSVUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:ratemaster-csv-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload csv file';

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
        Log::warning("CSV File upload executed handle!");

        $path = 'RateMaster/export-rate.csv';
        $file = Storage::path($path);
        $csv = Reader::createFromPath($file, 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);
        
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');
        
        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        $symbols = [' ', '-'];
    
        foreach($csv as $data)
        {   
            $shipntrack = R::dispense('ratemasters');
            foreach($data as $key => $result)
            {
                $header = str_replace($symbols, '_', strtolower($key));
                if($header)
                {
                    $shipntrack->$header = $result;
                    R::store($shipntrack);
                }     
            }
        }
        Log::warning("CSV Upload Successfully!");
    }
}
