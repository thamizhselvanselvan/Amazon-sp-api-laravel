<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use App\Events\testEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\OthercatDetailsIndia;
use Illuminate\Support\Facades\Storage;
use App\Models\otherCatalog\OtherCatalogAsin;

class exportOtherAmazonInProduct extends Command
{
    private $fileNameOffset = 0;
    private $check;
    private $count = 1;
    private $writer;
    private $totalProductCount;
    private $currentCount;
    private $headers_default;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:export-other-amazon-in {selected} {email} {id} {type}';

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
        $user = $this->argument('email');
        $id = $this->argument('id');
        $type = $this->argument('type');

        $headerSelection = explode('-', $selected);
        $headers = $headerSelection;

        if ($type == 'Asin') {
            $this->catalogExportByAsin($id, $user, $headers);
        } else {

            $exportFilePath = "excel/downloads/otheramazonIN/" . $user . "/otherProductDetails";
            $deleteFilePath = "app/excel/downloads/otheramazonIN/" . $user;

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

            $this->totalProductCount = OthercatDetailsIndia::count();

            OthercatDetailsIndia::select($headers)->chunk($chunk, function ($records) use ($exportFilePath, $headers, $chunk) {

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

                foreach ($records as $key => $data) {
                    Log::alert(json_encode($data));
                }
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
        $path = "app/excel/downloads/otheramazonIN/" . $user;
        $path = storage_path($path);
        $files = (scandir($path));

        $filesArray = [];
        foreach ($files as $key => $file) {
            if ($key > 1) {
                if (str_contains($file, '.mosh')) {
                    $new_file_name = str_replace('.csv.mosh', '.csv', $file);
                    rename($path . '/' . $file, $path . '/' . $new_file_name);
                }
            }
        }
    }

    public function catalogExportByAsin($id, $user, $headers)
    {
        foreach($headers as $header_value)
        {
            $this->headers_default[$header_value] = 'N/A' ;
        }
        // Log::notice($this->headers_default);
        $exportFilePath = "excel/downloads/otheramazonIN/" . $user . "/otherProductDetails";
        $deleteFilePath = "app/excel/downloads/otheramazonIN/" . $user;

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
        $selected_asin = OtherCatalogAsin::select('asin')->where('user_id', $id)->where('source', 'in')->get();

        $this->totalProductCount = count($selected_asin);
        // $this->totalProductCount = OthercatDetails::count();
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
        OthercatDetailsIndia::select($headers)->whereIn('asin', $selected_asin)->chunk($chunk, function ($records) use ($exportFilePath, $headers, $chunk, $selected_asin) {

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
            // Log::info($records);
            $all_asins = [];
            foreach($selected_asin as $asin) {

                if($val = array_search(trim($asin), array_column($records, 'asin'))) {

                    $all_asins[] = $records[$val];

                } else {

                    $this->headers_default['asin'] = $asin;
                    $all_asins[] = $this->headers_default;
                }
            }
            $this->writer->insertall($all_asins);

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
