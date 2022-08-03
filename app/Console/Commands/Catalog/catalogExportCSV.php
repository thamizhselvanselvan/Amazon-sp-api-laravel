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
    private $count = 1 ;
    private $country_code;
    private $remender;
    private $writer;
    private $csv_files = [];
    private $file_path;
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
        $total_csv = 1000000 ;
        $chunk = 100000 ;
        $this->remender = $total_csv / $chunk ;
        $this->country_code = $this->argument('country_code');
        
        $table_name = 'catalog'.strtolower($this->country_code).'s';
        $modal_table = table_model_create(country_code:$this->country_code, model:'Catalog', table_name:'catalog');
        $modal_table->orderBy('id')->chunk($chunk, function($result){

            if($this->count == 1 )
            {   
                $this->file_path = "excel/downloads/catalog/".$this->country_code."/Catalog-export".$this->country_code.$this->offset.".csv";
                $this->csv_files [] = "Catalog-export".$this->country_code.$this->offset.".csv";
                
                if(!Storage::exists($this->file_path))
                {
                    Storage::put($this->file_path, '');
                }
                
                $this->writer = Writer::createFromPath(Storage::path($this->file_path, 'w'));
                $header = ['S/N' ,'ASIN', 'Source', 'Binding', 'Brand', 'Item-Dimensions', 'Manufacturer',];
                $this->writer->insertOne($header);
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
            $this->writer->insertAll($records);
            
            if($this->remender == $this->count)
            {
                
                ++$this->offset;
                $this->count = 1;
                
            }
            else{
                
                ++$this->count;
            }
            
        });
    
        $zip = new ZipArchive;
        $path = "excel/downloads/catalog/".$this->country_code."/zip/Catalog".$this->country_code.".zip";
        $file_path = Storage::path($path);
        
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        
        if($zip->open($file_path, ZipArchive::CREATE) === TRUE)
        {
            foreach($this->csv_files as $key => $value)
            {
                $path = Storage::path('excel/downloads/catalog/'.$this->country_code.'/'.$value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
