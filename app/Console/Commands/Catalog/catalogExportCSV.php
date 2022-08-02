<?php

namespace App\Console\Commands\Catalog;

use ZipArchive;
use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class catalogExportCSV extends Command
{
    private $offset = 0 ;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-export-csv {country_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Catalog export into csv file';

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
        $count = 1 ;
        $total_csv = 50 ;
        $chunk = 10 ;
        $remender = $total_csv / $chunk ;
       
        $country_code = $this->argument('country_code');
        
        $table_name = 'catalog'.strtolower($country_code).'s';
       

        $data = DB::connection('catalog')->select(" SELECT * from $table_name ");
        // Log::alert($data);
        foreach(array_chunk($data, $chunk) as $result)
        {
            $records = [];
            if($count == 1 )
            {   
                $file_path = "excel/downloads/catalog/".$country_code."/Catalog-export".$country_code.$this->offset.".csv";
                $csv_files [] = "Catalog-export".$country_code.$this->offset.".csv";
                if(!Storage::exists($file_path))
                {
                    Storage::put($file_path, '');
                }
                $writer = Writer::createFromPath(Storage::path($file_path, 'w'));
                $header = ['S/N' ,'ASIN', 'Source', 'Binding', 'Brand', 'Item-Dimensions', 'Manufacturer',];
                $writer->insertOne($header);
                   
            }
            foreach($result as $value)
            {
                $records []= [
                    'S/N' => $value->id,
                    'ASIN' => $value->asin,
                    'Source' => $value->source,
                    'Binding' => $value->binding,
                    'Brand' => $value->brand,
                    'Item-Dimensions' =>$value->item_dimensions,
                    'Manufacturer' => $value->manufacturer,
                ];
            }
            $writer->insertAll($records);
            
            if($remender == $count)
            {
                
                $this->offset++;
                $count = 1;
                
            }
            else{
                
                $count++;
            }   
        }

        $zip = new ZipArchive;
        $path = "excel/downloads/catalog/".$country_code."/zip/Catalog".$country_code.".zip";
        $file_path = Storage::path($path);
        
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        
        if($zip->open($file_path, ZipArchive::CREATE) === TRUE)
        {
            foreach($csv_files as $key => $value)
            {
                $path = Storage::path('excel/downloads/catalog/'.$country_code.'/'.$value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
