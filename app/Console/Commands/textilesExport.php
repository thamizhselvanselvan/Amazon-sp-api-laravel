<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use SplTempFileObject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
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
                
             $records = DB::select('select textile_id, ean, brand, title, size, color, transfer_price, shipping_weight, product_type, quantity from sp_universal_textiles ');
        
        } else {

            $records = DB::select('select textile_id, ean, brand, title, size, color, transfer_price, shipping_weight, product_type, quantity from sa_universal_textiles limit 200 ');
            
        }

        $records = array_map(function ($datas) {

            $datas->size = "'".$datas->size."'";

            return (array) $datas;
        }, $records);
        
        // dd($records);
        $header = ['textile_id', 'ean', 'brand', 'title', 'size', 'color', 'transfer_price', 'shipping_weight', 'product_type', 'quantity'];

        $file_path = "excel\\downloads\\universalTextilesExport.csv";

        if(!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }

        if(!Storage::exists($file_path)) {
            return false;
        }
        
        $writer = Writer::createFromPath(Storage::path($file_path), "w"); //the CSV file will be created using a temporary File

        $writer->insertOne($header);
        $writer->insertAll($records);

        $url = Storage::url('app\\'.$file_path);

        echo asset($url);     

    }
}
