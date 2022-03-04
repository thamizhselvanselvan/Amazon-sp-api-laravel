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
        $file_path = "excel/downloads/universalTextilesExport.csv";
        if(!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        $writer = Writer::createFromPath(Storage::path($file_path), "w"); 
        $header = ['S/N','Textile Id', 'Ean', 'Brand', 'Title', 'Size', 'Color', 'Transfer Price', 'Shipping Weight', 'Product Type', 'Quantity','Created At','Updated At'];
        $writer->insertOne($header);

            DB::table('universal_textiles')->orderBy('id')->chunk(10000, function ($records) use($writer) {

            $records = $records->toArray();
            $records = array_map(function ($datas) {
                $datas->size = "'".$datas->size."'";
                return (array) $datas;
                }, $records);

            $writer->insertall($records);
                    
        });
    }
}
