<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use App\Events\testEvent;
use App\Models\OthercatDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
    protected $signature = 'pms:export-other-amazon {selected} {email} {id} {type}';

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
        // Log::alert('working');
        $selected = $this->argument('selected');
        $user = $this->argument('email');
        $id = $this->argument('id');
        $type = $this->argument('type');

        $headerSelection = explode('-', $selected);
        $headers = $headerSelection;

        if ($type == 'Asin') {

            $this->catalogExportByAsin($id, $user, $headers);
        } else {

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

            $record_per_csv = 1000000;
            $chunk = 100000;

            $this->check = $record_per_csv / $chunk;

            $this->totalProductCount = OthercatDetails::count();

            OthercatDetails::select($headers)->chunk($chunk, function ($records) use ($exportFilePath, $headers, $chunk) {

                if ($this->count == 1) {
                    if (!Storage::exists($exportFilePath . $this->fileNameOffset . '.csv.mosh')) {
                        Storage::put($exportFilePath . $this->fileNameOffset . '.csv.mosh', '');
                    }
                    $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $this->fileNameOffset . '.csv.mosh'), "w");
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
                $percentage = ceil(round($this->currentCount * 100) / $this->totalProductCount);

                if ($percentage > 100) {
                    $percentage = 100;
                }
                event(new testEvent($percentage));
            });
        }

         //remame file .mosh to .csv
         $path = "app/excel/downloads/otheramazon/" . $user;
         $path = storage_path($path);
         $files = (scandir($path));
 
         $filesArray = [];
         foreach ($files as $key => $file) {
             if ($key > 1) {
                 if(str_contains($file, '.mosh'))
                 {
                     $new_file_name = str_replace('.csv.mosh', '.csv', $file);
                     rename($path.'/'.$file, $path.'/'.$new_file_name);
                 }
             }
         }
    }

    public function catalogExportByAsin($id, $user, $headers)
    {
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

        $record_per_csv = 1000000;
        $chunk = 5000;

        $this->check = $record_per_csv / $chunk;
        $selected_asin = OtherCatalogAsin::select('asin')->where('user_id', $id)->where('source', 'com')->get();

        $this->totalProductCount = count($selected_asin);
        // $this->totalProductCount = OthercatDetails::count();
        // Log::alert('count' . $this->totalProductCount);

        $selected_count = 0;
        $chunk_asin = [];
        foreach ($selected_asin as $asin) {

            $chunk_asin[] = $asin->asin;

            if ($selected_count == 5000) {
                $this->chunkAsinDetails($headers, $chunk_asin, $chunk, $exportFilePath);
                $selected_count = 0;
                $chunk_asin = NULL;
            } else {
                $selected_count++;
            }
        }
        $this->chunkAsinDetails($headers, $chunk_asin, $chunk, $exportFilePath);
    }

    public function chunkAsinDetails($headers, $selected_asin, $chunk, $exportFilePath)
    {
        OthercatDetails::select($headers)->whereIn('asin', $selected_asin)->chunk($chunk, function ($records) use ($exportFilePath, $headers, $chunk, $selected_asin) {

            if ($this->count == 1) {
                if (!Storage::exists($exportFilePath . $this->fileNameOffset . '.csv.mosh')) {
                    Storage::put($exportFilePath . $this->fileNameOffset . '.csv.mosh', '');
                }
                $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $this->fileNameOffset . '.csv.mosh'), "w");
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
            $percentage = ceil(round($this->currentCount * 100) / $this->totalProductCount);

            if ($percentage > 100) {
                $percentage = 100;
            }
            event(new testEvent($percentage));
        });

        return true;
    }
}
