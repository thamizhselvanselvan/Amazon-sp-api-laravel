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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-price-export-csv {priority} {country_code} {headers}';

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
        $this->country_code = $this->argument('country_code');
        $this->priority = $this->argument('priority');
        $selected_headers = explode(',', $this->argument('headers'));



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

            $str = ['destination.', 'cat.', 'pricing_ins.'];
            // log::notice($selected_headers);
            foreach ($selected_headers as $key => $selected_header) {
                $headers[] = "${selected_header}";
                $csv_title = str_replace($str, '', $selected_header);
                if ($csv_title == 'images') {
                    $csv_head[] = ['image1', 'image2'];
                }
                if ($csv_title == 'dimensions') {
                    $csv_head[] = ['height', 'length', 'width', 'unit'];
                }
                if ($selected_header != 'cat.images' && $selected_header != 'cat.dimensions') {
                    $csv_head[$key][] = str_replace('_', ' ', $csv_title);
                }
            }
            foreach ($csv_head as $csv_heading) {
                foreach ($csv_heading as $csv) {

                    $csv_header[] = $csv;
                }
            }
            log::notice($csv_header);
            // exit;
            PricingIn::select($headers)
                ->rightJoin('asin_destination_ins as destination', 'pricing_ins.asin', '=', 'destination.asin')
                ->leftJoin("catalognewins as cat", 'destination.asin', '=', 'cat.asin')
                ->where('destination.priority', $this->priority)
                ->orWhereNull('destination.asin')
                ->chunk($chunk, function ($records) use ($exportFilePath, $csv_header, $chunk) {

                    $this->CreateCsvFile($csv_header, $records, $exportFilePath);
                    //pusher
                });
        } elseif ($this->country_code == 'US') {

            $str = ['destination.', 'cat.', 'pricing_uss.'];
            foreach ($selected_headers as $key => $selected_header) {
                $headers[] = "${selected_header}";
                $csv_title = str_replace($str, '', $selected_header);
                if ($csv_title == 'images') {
                    $csv_head[] = ['image1', 'image2'];
                }
                if ($csv_title == 'dimensions') {
                    $csv_head[] = ['height', 'length', 'width', 'unit'];
                }
                if ($selected_header != 'cat.images' && $selected_header != 'cat.dimensions') {
                    $csv_head[$key][] = str_replace('_', ' ', $csv_title);
                }
            }
            foreach ($csv_head as $csv_heading) {
                foreach ($csv_heading as $csv) {

                    $csv_header[] = $csv;
                }
            }
            log::notice($csv_header);

            PricingUs::select($headers)
                ->rightJoin('asin_destination_uss as destination', 'pricing_uss.asin', '=', 'destination.asin')
                ->leftJoin("catalognewuss as cat", 'destination.asin', '=', 'cat.asin')
                ->where('destination.priority', $this->priority)
                ->orWhereNull('destination.asin')
                ->chunk($chunk, function ($records) use ($exportFilePath, $csv_header, $chunk) {

                    $this->CreateCsvFile($csv_header, $records, $exportFilePath);
                    //pusher
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

                // $not_available[$key][$key2] = $value ?? "NA";

                if ($key2 == 'images') {
                    $images = json_decode($value);
                    $image = isset($images[0]->images) ? $images[0]->images : 'NA';
                    $not_available[$key]['image1'] = isset($image[0]->link) ? $image[0]->link : 'NA';
                    $not_available[$key]['image2'] = isset($image[1]->link) ? $image[1]->link : 'NA';
                }
                if ($key2 == 'product_types') {
                    $product_types = json_decode($value);
                    $not_available[$key]['product_types'] = isset($product_types[0]->productType) ? $product_types[0]->productType : 'NA';
                }

                if ($key2 == 'updated_at') {
                    $not_available[$key]['updated_at'] = isset($record['updated_at']) ? date("d-m-Y h:i:s", strtotime($record['updated_at'])) : 'NA';
                }

                if ($key2 == 'dimensions') {

                    $dimension = json_decode($value);
                    $package = isset($dimension[0]->package) ? $dimension[0]->package : 'NA';

                    $not_available[$key]['height'] = isset($package->height->value) ? $package->height->value : 'NA';
                    $not_available[$key]['length'] = isset($package->length->value) ? $package->length->value : 'NA';
                    $not_available[$key]['width'] = isset($package->width->value) ? $package->width->value : 'NA';
                    $not_available[$key]['unit'] = isset($package->width->unit) ? $package->width->unit : 'NA';
                    // $not_available[$key]['weight'] = isset($package->weight->value) ? $package->weight->value : 'NA';
                    // $not_available[$key]['weight_unit'] = isset($package->weight->unit) ? $package->weight->unit : 'NA';
                }


                if ($key2 != 'dimensions' && $key2 != 'updated_at' && $key2 != 'images' && $key2 != 'product_types' && $key2 != 'updated_at') {

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
