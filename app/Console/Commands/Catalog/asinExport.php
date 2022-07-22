<?php

namespace App\Console\Commands\Catalog;

use ZipArchive;
use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class asinExport extends Command
{
    private $offset = 0;
    private $count = 1;
    private $mode ;
    private $writer;
    private $file_path;
    protected $Files = [];
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
        // log::alert(gettype($this->Files));
        $total_csv = 1000000;
        $chunk = 100000;
        $this->mode = $total_csv / $chunk;

        Asin_master::orderBy('id')->chunk($chunk, function ($records) {
            
            if($this->count == 1 ){

                $this->file_path = "excel/downloads/asins/asinExport".$this->offset.".csv";
                $this->Files []= 'asinExport'.$this->offset.'.csv';
                if(!Storage::exists($this->file_path)) {
                    Storage::put($this->file_path, '');
                    }
                $this->writer = Writer::createFromPath(Storage::path($this->file_path), "w"); 
                $header = ['S/N','Asin', 'Source', 'Destination 1', 'Destination 2', 'Destination 3', 'Destination 4', 'Destination 5', 'Created At','Updated At'];
                $this->writer->insertOne($header);

            }
            $records = $records->toArray();
            $this->writer->insertall($records);

            if($this->mode == $this->count){
                $this->offset++;
                $this->count = 1;
            }
            else{
                $this->count++;
            }
            

        });
        
        $zip = new ZipArchive;
        $path = 'excel/downloads/asins/zip/CatalogAsin.zip';
        $file_path = Storage::path($path);
        
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        
        if($zip->open($file_path, ZipArchive::CREATE) === TRUE)
        {
            foreach($this->Files as $key => $value)
            {
                $path = Storage::path('excel/downloads/asins/'.$value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
