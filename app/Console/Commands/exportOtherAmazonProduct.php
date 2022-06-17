<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use App\Events\testEvent;
use App\Models\OthercatDetails;
use Illuminate\Console\Command;
use function PHPUnit\Framework\at;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use App\Models\otherCatalog\OtherCatalogAsin;

class exportOtherAmazonProduct extends Command
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
    protected $signature = 'pms:export-other-amazon {selected} {email} {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export AWS other_amazon product table into csv with realtime progress';

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
        $user = $this->argument('email');
        $id = $this->argument('id');
        
        Log::alert('working');
        $headerSelection = explode('-', $selected);
        $headers = $headerSelection;

        $exportFilePath = "excel/downloads/otheramazon/" . $user . "/otherProductDetails";
        $deleteFilePath = "app/excel/downloads/otheramazon/" . $user;

        if (file_exists(storage_path($deleteFilePath))) {
            $path = storage_path($deleteFilePath);
            $files = (scandir($path));
            foreach ($files as $key => $file) {
                if ($key > 1) {
                    unlink($path . '/' . $file);
                }
            }
        }

        Log::alert('working 1');

        $record_per_csv = 1000000;
        $chunk = 5000;

        $this->check = $record_per_csv / $chunk;
        $selected_asin = OtherCatalogAsin::select('asin')->where('user_id', $id)->get();
      
        $this->totalProductCount = count($selected_asin);
        Log::alert('count' . $this->totalProductCount);
        
        $selected_count = 0;
        $chunk_asin = [];
        foreach ($selected_asin as $asin) {

            $chunk_asin[] = $asin->asin;
            
            if ($selected_count == 5000) {
                $this->chunkAsinDetails($headers, $chunk_asin, $chunk, $exportFilePath);
                $selected_count = 0;
                $chunk_asin = NULL;
            }
            $selected_count++;
        }
        $this->chunkAsinDetails($headers, $chunk_asin, $chunk, $exportFilePath);
    }

    public function chunkAsinDetails($headers, $selected_asin, $chunk, $exportFilePath)
    {
        OthercatDetails::select($headers)->whereIn('asin', $selected_asin)->chunk($chunk, function ($records) use ($exportFilePath, $headers, $chunk, $selected_asin) {

            if ($this->count == 1) {
                if (!Storage::exists($exportFilePath . $this->fileNameOffset . '.csv')) {
                    Storage::put($exportFilePath . $this->fileNameOffset . '.csv', '');
                }
                $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $this->fileNameOffset . '.csv'), "w");
                $this->writer->insertOne($headers);
            }
            
            $records1 = $records->toArray();
            $records1 = array_map(function ($datas) {
                return $datas['asin'];
            }, $records1);

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
            $percentage = ceil(round($this->currentCount * 100) / $this->totalProductCount);

            if ($percentage > 100) {
                $percentage = 100;
            }
            event(new testEvent($percentage));
        });

        return true;
    }
}
