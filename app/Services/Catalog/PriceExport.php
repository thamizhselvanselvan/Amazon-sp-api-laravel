<?php

namespace App\Services\Catalog;

use ZipArchive;
use League\Csv\Writer;
use App\Models\Catalog\PricingAe;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingSa;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PriceExport
{
    private $count = 0;
    private $totalFile = [];
    private $country_code;
    private $fm_id;
    private $writer;
    private $record_per_csv = 1000000;
    private $chunk_limit = 50000;
    private $export_file_path;
    private $fileNameOffset = 0;
    private $country_codes = ['ae' => 'AE', 'in' => 'IN', 'sa' => 'SA', 'us' => 'US'];
    private $headers = [
        'in' => [
            'asin as Asin' =>  'Asin',
            'available' => 'Available',
            'weight' => 'Weight',
            'in_price' => 'IND Price',
            'ind_to_uae' => 'IND To UAE',
            'ind_to_sg' => 'IND To SG',
            'ind_to_sa' => 'IND To SA',
            'updated_at' => 'Updated At'
        ],
        'us' => [
            'asin as Asin' => 'Asin',
            'available' => 'Available',
            'weight' => 'Weight',
            'us_price' => 'US Price',
            'usa_to_in_b2b' => 'US to IND B2B',
            'usa_to_in_b2c' => 'US to IND B2C',
            'usa_to_uae' => 'US to UAE',
            'usa_to_sg' => 'US to SG',
            'updated_at' => 'Updated At',
        ],
        'ae' => [
            'asin as Asin' => 'Asin',
            'available' => 'Available',
            'weight' => 'Weight',
            'ae_price' => 'AE Price',
            'updated_at' => 'Updated At',
        ],
        'sa' => [
            'asin as Asin' => 'Asin',
            'available' => 'Available',
            'weight' => 'Weight',
            'sa_price' => 'SA Price',
            'updated_at' => 'Updated At',
        ]
    ];

    public function index($country_code, $fm_id, $priority)
    {

        $this->country_code = $country_code;
        $this->fm_id = $fm_id;
        $this->priority = $priority;

        $this->export_file_path = "excel/downloads/catalog_price/$country_code/Priority" . $priority . '/' . $country_code . "_CatalogPrice";

        if (!isset($this->country_codes[$country_code])) {
            Log::error($country_code . " Country Code Not found in our Database");
            return false;
        }

        $select_headers = array_keys($this->headers[$country_code]);
        $csv_headers = array_values($this->headers[$country_code]);

        $destination_model  = table_model_create(country_code: $country_code, model: 'Asin_destination', table_name: 'asin_destination_');
        $destination_ids = $destination_model->select('id')->where('priority', $priority)->orderBy('id', 'asc')->count();

        $pages = ceil($destination_ids / $this->chunk_limit);

        $start_id = 0;
        for ($page = 0; $page < $pages; $page++) {

            $asin_destination_lists = $destination_model->select('id', 'asin', 'priority')
                ->where('priority', $priority)
                ->limit($this->chunk_limit)
                ->get();

            $asin_collections = $asin_destination_lists->pluck('asin');

            $pricing_model = table_model_create(country_code: $country_code, model: 'Pricing', table_name: 'pricing_');
            $records = $pricing_model->select($select_headers)->whereIn('asin', $asin_collections)->get()->toArray();

            $this->createCsv($csv_headers, $records);

            $start_id += $this->chunk_limit;
        }


        $this->createZip();
    }

    public function createCsv($csv_header, $records)
    {

        if ($this->count == 0) {
            if (!Storage::exists($this->export_file_path . $this->fileNameOffset . '.csv')) {
                Storage::put($this->export_file_path . $this->fileNameOffset . '.csv', '');
            }
            $this->totalFile[] = $this->country_code . "_CatalogPrice" . $this->fileNameOffset . '.csv';
            $this->writer = Writer::createFromPath(Storage::path($this->export_file_path . $this->fileNameOffset . '.csv'), "w");
            $this->writer->insertOne($csv_header);
        }

        $this->writer->insertAll($records);

        $this->count += $this->chunk_limit;

        if ($this->record_per_csv == $this->count) {
            $this->fileNameOffset++;
            $this->count = 0;
        }

        return true;
    }

    public function createZip()
    {
        $zip = new ZipArchive;
        $path = "excel/downloads/catalog_price/" . $this->country_code . '/Priority' . $this->priority . '/' . "/zip/" . $this->country_code . "_CatalogPrice.zip";

        $file_path = Storage::path($path);

        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }

        $files_to_delete = [];

        if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
            foreach ($this->totalFile as $key => $value) {

                $path = Storage::path('excel/downloads/catalog_price/' . $this->country_code . '/Priority' . $this->priority . '/' . $value);
                $files_to_delete[] = $path;
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();

            foreach ($files_to_delete as $file_path_value) {
                unlink($file_path_value);
            }
        }

        // FILE MANAGEMENT UPDATE
        $command_end_time = now();
        fileManagementUpdate($this->fm_id, $command_end_time);
    }
}
