<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class asinExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:asin-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Asin from DB to CSV';

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
       $file_path = "excel/downloads/asins/asinExport.csv";
       if(!Storage::exists($file_path)) {
        Storage::put($file_path, '');
    }
    
    $writer = Writer::createFromPath(Storage::path($file_path), "w"); 
    $header = ['S/N','Asin', 'Source', 'Destination 1', 'Destination 2', 'Destination 3', 'Destination 4', 'Destination 5', 'Created At','Updated At'];
    $writer->insertOne($header);
    
    DB::table('asin_masters')->orderBy('id')->limit(100)->chunk(10, function ($records) use( $writer) {
        
        $records = $records->toArray();
        $records = array_map(function ($datas) {
            return (array) $datas;
        }, $records);
        
           $writer->insertall($records);
                   
        });
        
    }
}
