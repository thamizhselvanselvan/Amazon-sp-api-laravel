<?php

namespace App\Console\Commands\Catalog;

use ZipArchive;
use League\Csv\Writer;
use Illuminate\Console\Command;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CatalogPriceExportCSV extends Command
{
    private $fileNameOffset = 0;
    private $check;
    private $count = 1;
    private $writer;
    private $totalProductCount;
    private $currentCount;
    private $headers_default;
    private $totalFile = [];
    private $country_code;
    private $priority;
    private $date;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'mosh:catalog-price-export-csv {priority} {destination} {fm_id}';
    protected $signature = 'mosh:catalog-price-export-csv {--columns=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export catalog Price in CSV accroding to Country code';

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
        $columns_data = $this->option('columns');
        $final_data = [];
        $explode_array = explode(',', $columns_data);
        foreach ($explode_array as $value) {
            list($key, $value) = explode('=', $value);
            $final_data[$key] = $value;
        }

        $fm_id = $final_data['fm_id'];
        $this->country_code = $final_data['destination'];
        $this->priority = $final_data['priority'];

        // $date = $this->date = $this->argument('date');
        // $fm_id = $this->argument('fm_id');
        // $this->country_code = $this->argument('destination');
        // $this->priority = $this->argument('priority');
        // $this->date = $this->argument('date');
        // $selected_headers = explode(',', $this->argument('headers'));
        $start_date = '';
        $end_date = '';
        // $data = explode(' - ', $date);
        // if (count($data) >= 2) {
        //     $split = [trim($data[0]), trim($data[1])];
        //     $range = [
        //         $split[0],
        //         $split[1]
        //     ];

        //     $selected_date = implode(',', $range);
        //     $date = explode(',', $selected_date);
        //     $start_date = $date[0];
        //     $end_date = $date[1];
        // }

        if ($start_date == '') {

            $exportFilePath = "excel/downloads/catalog_price/$this->country_code/Priority" . $this->priority . '/' . $this->country_code . "_CatalogPrice";
            $deleteFilePath = "app/excel/downloads/catalog_price/" . $this->country_code;

            // if (file_exists(storage_path($deleteFilePath))) {
            //     $path = storage_path($deleteFilePath);
            //     $files = (scandir($path));
            //     foreach ($files as $key => $file) {
            //         if ($key > 1) {
            //             unlink($path . '/' . $file);
            //         }
            //     }
            // }
            $csv_head = [];
            $csv_header = [];

            $record_per_csv = 1000000;
            $chunk = 20000;
            $this->check = $record_per_csv / $chunk;

            if ($this->country_code == 'IN') {

                $headers = [
                    'destination.asin as Asin',
                    'pricing_ins.available',
                    'pricing_ins.weight',
                    'pricing_ins.in_price',
                    'pricing_ins.ind_to_uae',
                    'pricing_ins.ind_to_sg',
                    'pricing_ins.ind_to_sa',
                    'pricing_ins.updated_at',
                ];

                $csv_header = [
                    'Asin',
                    'Available',
                    'Weight',
                    'IND Price',
                    'IND To UAE',
                    'IND To SG',
                    'IND To SA',
                    'Updated At'
                ];

                PricingIn::select($headers)
                    ->rightJoin('asin_destination_ins as destination', 'pricing_ins.asin', '=', 'destination.asin')
                    ->where('destination.priority', $this->priority)
                    ->orWhereNull('destination.asin')
                    ->chunk($chunk, function ($records) use ($exportFilePath, $csv_header, $chunk) {


                        $this->CreateCsvFile($csv_header, $records, $exportFilePath);
                    });
            } elseif ($this->country_code == 'US') {

                $headers = [
                    'destination.asin as Asin',
                    'pricing_uss.available',
                    'pricing_uss.weight',
                    'pricing_uss.us_price',
                    'pricing_uss.usa_to_in_b2b',
                    'pricing_uss.usa_to_in_b2c',
                    'pricing_uss.usa_to_uae',
                    'pricing_uss.usa_to_sg',
                    'pricing_uss.updated_at',
                ];

                $csv_header = [
                    'Asin',
                    'Available',
                    'Weight',
                    'US Price',
                    'USA To IND B2B',
                    'USA To IND B2C',
                    'USA To UAE',
                    'USA To Singapore',
                    'Updated At'
                ];

                PricingUs::select($headers)
                    ->rightJoin('asin_destination_uss as destination', 'pricing_uss.asin', '=', 'destination.asin')
                    ->where('destination.priority', $this->priority)
                    ->orWhereNull('destination.asin')
                    ->chunk($chunk, function ($records) use ($exportFilePath, $csv_header, $chunk) {

                        $this->CreateCsvFile($csv_header, $records, $exportFilePath);
                    });
            }

            $path = "excel/downloads/catalog_price/" . $this->country_code . '/Priority' . $this->priority;
            $path = Storage::path($path);
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

            $zip = new ZipArchive;
            $path = "excel/downloads/catalog_price/" . $this->country_code . '/Priority' . $this->priority . '/' . "/zip/" . $this->country_code . "_CatalogPrice.zip";
            $file_path = Storage::path($path);
            if (!Storage::exists($path)) {
                Storage::put($path, '');
            }
            if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
                foreach ($this->totalFile as $key => $value) {
                    $path = Storage::path('excel/downloads/catalog_price/' . $this->country_code . '/Priority' . $this->priority . '/' . $value);
                    $relativeNameInZipFile = basename($path);
                    $zip->addFile($path, $relativeNameInZipFile);
                }
                $zip->close();
            }
        } else {

            $exportFilePath = "excel/downloads/catalog_price/$this->country_code/Priority" . $this->priority . '/' . $this->country_code . "_CatalogPrice";
            $deleteFilePath = "app/excel/downloads/catalog_price/" . $this->country_code;

            $csv_head = [];
            $csv_header = [];

            $record_per_csv = 1000000;
            $chunk = 20000;
            $this->check = $record_per_csv / $chunk;

            if ($this->country_code == 'IN') {

                $headers = [
                    'destination.asin as Asin',
                    'pricing_ins.available',
                    'pricing_ins.weight',
                    'pricing_ins.in_price',
                    'pricing_ins.ind_to_uae',
                    'pricing_ins.ind_to_sg',
                    'pricing_ins.ind_to_sa',
                    'pricing_ins.updated_at',
                ];

                $csv_header = [
                    'Asin',
                    'Available',
                    'Weight',
                    'IND Price',
                    'IND To UAE',
                    'IND To SG',
                    'IND To SA',
                    'Updated At'
                ];

                PricingIn::select($headers)
                    ->rightJoin('asin_destination_ins as destination', 'pricing_ins.asin', '=', 'destination.asin')
                    ->where('destination.priority', $this->priority)
                    ->whereBetween('pricing_ins.updated_at', [$start_date . " 00:00:01", $end_date . " 23:59:59"])
                    ->orWhereNull('destination.asin')
                    ->chunk($chunk, function ($records) use ($exportFilePath, $csv_header, $chunk) {


                        $this->CreateCsvFile($csv_header, $records, $exportFilePath);
                    });
            } elseif ($this->country_code == 'US') {

                $headers = [
                    'destination.asin as Asin',
                    'pricing_uss.available',
                    'pricing_uss.weight',
                    'pricing_uss.us_price',
                    'pricing_uss.usa_to_in_b2b',
                    'pricing_uss.usa_to_in_b2c',
                    'pricing_uss.usa_to_uae',
                    'pricing_uss.usa_to_sg',
                    'pricing_uss.updated_at',
                ];

                $csv_header = [
                    'Asin',
                    'Available',
                    'Weight',
                    'US Price',
                    'USA To IND B2B',
                    'USA To IND B2C',
                    'USA To UAE',
                    'USA To Singapore',
                    'Updated At'
                ];

                PricingUs::select($headers)
                    ->rightJoin('asin_destination_uss as destination', 'pricing_uss.asin', '=', 'destination.asin')
                    ->where('destination.priority', $this->priority)
                    ->whereBetween('pricing_uss.updated_at', [$start_date . " 00:00:01", $end_date . " 23:59:59"])
                    ->orWhereNull('destination.asin')
                    ->chunk($chunk, function ($records) use ($exportFilePath, $csv_header, $chunk) {

                        $this->CreateCsvFile($csv_header, $records, $exportFilePath);
                    });
            }

            $path = "excel/downloads/catalog_price/" . $this->country_code . '/Priority' . $this->priority;
            $path = Storage::path($path);
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

            $zip = new ZipArchive;
            $path = "excel/downloads/catalog_price/" . $this->country_code . '/Priority' . $this->priority . '/' . "/zip/" . $this->country_code . "_CatalogPrice.zip";
            $file_path = Storage::path($path);
            if (!Storage::exists($path)) {
                Storage::put($path, '');
            }
            if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
                foreach ($this->totalFile as $key => $value) {
                    $path = Storage::path('excel/downloads/catalog_price/' . $this->country_code . '/Priority' . $this->priority . '/' . $value);
                    $relativeNameInZipFile = basename($path);
                    $zip->addFile($path, $relativeNameInZipFile);
                }
                $zip->close();
            }
        }

        // FILE MANAGEMENT UPDATE
        $command_end_time = now();
        fileManagementUpdate($fm_id, $command_end_time);
    }
    public function CreateCsvFile($csv_header, $records, $exportFilePath)
    {

        if ($this->count == 1) {
            if (!Storage::exists($exportFilePath . $this->fileNameOffset . '.csv.mosh')) {
                Storage::put($exportFilePath . $this->fileNameOffset . '.csv.mosh', '');
            }
            $this->totalFile[] = $this->country_code . "_CatalogPrice" . $this->fileNameOffset . '.csv';
            $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $this->fileNameOffset . '.csv.mosh'), "w");
            $this->writer->insertOne($csv_header);
        }

        $records = $records->toArray();
        $records = array_map(function ($datas) {
            return (array) $datas;
        }, $records);

        $not_available = [];

        foreach ($records as $key => $record) {

            foreach ($record as $key2 => $value) {

                if ($key2 == 'updated_at') {
                    $not_available[$key]['updated_at'] = isset($record['updated_at']) ? date("d-m-Y h:i:s", strtotime($record['updated_at'])) : 'NA';
                }


                if ($key2 != 'updated_at') {

                    $not_available[$key][$key2] = $value ?? 'NA';
                }
            }
        }

        $this->writer->insertall($not_available);

        if ($this->check == $this->count) {
            $this->fileNameOffset++;
            $this->count = 1;
        } else {
            ++$this->count;
        }
        return true;
    }
}
