<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use SplTempFileObject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class textilesExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:textiles-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Universal textils from DB';

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
        
         if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
                
             $records = DB::select('select textile_id, ean, brand, title, size, color, transfer_price, shipping_weight, product_type, quantity from sp_universal_textiles');
        Log::warning('production DB query executed');
        } else {

            $records = DB::select('select textile_id, ean, brand, title, size, color, transfer_price, shipping_weight, product_type, quantity from sa_universal_textiles');
            
        }

        $records = array_map(function ($datas) {

                $datas->size = "'".$datas->size."'";
                return (array) $datas;
        }, $records);
            
        $header = ['textile_id', 'ean', 'brand', 'title', 'size', 'color', 'transfer_price', 'shipping_weight', 'product_type', 'quantity'];

        Log::warning('array mapping completed');
    
        $file_path = "excel/downloads/universalTextilesExport.csv";
            if(!Storage::exists($file_path)) {
                Storage::put($file_path, '');
            }
            Log::notice('Working 2');
            if(!Storage::exists($file_path)) {
                return false;
            }
            
            Log::warning('csv writing stated');
            $writer = Writer::createFromPath(Storage::path($file_path), "w"); //the CSV file will be created using a temporary File
    
            $writer->insertOne($header);
            $writer->insertAll($records);
            
            // Log::notice('csv writing commpleted');

    }
}
