<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use App\Events\testEvent;
use App\Models\OthercatDetailsIndia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class exportOtherAmazonInProduct extends Command
{
    private $fileNameOffset = 0;
    private $check;
    private $count = 1;
    private $writer;
    private $totalProductCount;
    private $currentCount;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:export-other-amazon-in {selected} {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export AWS other_amazon.in product table into csv with realtime progress';

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
        $headerSelection = '';
        
        $selected = $this->argument('selected');
        $user = $this->argument('user');

        $headerSelection = explode('-', $selected);
        $headers = $headerSelection;
        
        $exportFilePath = "excel/downloads/otheramazonIN/".$user."/otherProductDetails";
        $deleteFilePath = "app/excel/downloads/otheramazonIN/".$user;

         if(file_exists(storage_path($deleteFilePath))){
             $path = storage_path($deleteFilePath);
             $files = (scandir($path));
            foreach ($files as $key => $file) {
                if ($key > 1) {
                    unlink($path.'/'.$file);
                }
            }
        }

        $record_per_csv = 1000000;
        $chunk = 100000;
        
        $this->check = $record_per_csv / $chunk;

        $this->totalProductCount = OthercatDetailsIndia::count();

        OthercatDetailsIndia::select($headers)->chunk($chunk, function ($records) use ($exportFilePath, $headers, $chunk) {

            if ($this->count == 1) {
                if (!Storage::exists($exportFilePath . $this->fileNameOffset . '.csv')) {
                    Storage::put($exportFilePath . $this->fileNameOffset . '.csv', '');
                }
                $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $this->fileNameOffset . '.csv'), "w");
                $this->writer->insertOne($headers);
            }

            $records = $records->toArray();
            $records = array_map(function ($datas) {
                return (array) $datas;
            }, $records);

            $this->writer->insertall($records);

            if ($this->check == $this->count) {
                $this->fileNameOffset++;
                $this->count = 1;
            } else {
                ++$this->count;
            }

            $this->currentCount += $chunk;
            $percentage = ceil(round($this->currentCount *100) / $this->totalProductCount);

            if($percentage > 100) {
                $percentage = 100;
            }
            event(new testEvent($percentage));
        });
    }
}