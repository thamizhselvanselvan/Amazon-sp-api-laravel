<?php

namespace App\Services\Catalog;

use App\Models\Catalog\Catalog_ae;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_sa;
use App\Models\Catalog\Catalog_us;
use ZipArchive;
use League\Csv\Writer;
use App\Models\Catalog\PricingAe;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingSa;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AllPriceExportCsvServices
{
    private $count = 1;
    private $totalFile = [];
    private $country_code;
    private $writer;
    private $record_per_csv = 1000000;
    private $export_file_path;
    private $fileNameOffset = 0;
    private $priority;

    public function index($country_code, $fm_id, $priority = 'All')
    {
        $this->country_code = strtoupper($country_code);
        $this->priority = $priority;

        $chunk = 4000;

        $us_csv_header = [
            'Priority',
            'Asin',
            'Available',
            'Weight',
            'Length',
            'Width',
            'Height',
            'Unit',
            'US Price',
            'USA To IND B2B',
            'USA To IND B2C',
            'USA To UAE',
            'USA To SG',
            'Price Updated At'
        ];

        $us_select = [
            'asin',
            'available',
            'weight',
            'us_price',
            'usa_to_in_b2b',
            'usa_to_in_b2c',
            'usa_to_uae',
            'usa_to_sg',
            'price_updated_at'
        ];

        $in_csv_header = [
            'Priority',
            'Asin',
            'Available',
            'Weight',
            'Length',
            'Width',
            'Height',
            'Unit',
            'IN Price',
            'IND To UAE',
            'IND To SG',
            'IND To SA',
            'Price Updated At'
        ];

        $in_select = [
            'asin',
            'available',
            'weight',
            'in_price',
            'ind_to_uae',
            'ind_to_sg',
            'ind_to_sa',
            'price_updated_at',
        ];

        $ae_csv_header = [
            'Priority',
            'Asin',
            'Available',
            'Weight',
            'Length',
            'Width',
            'Height',
            'Unit',
            'AE Price',
            'Price Updated At'
        ];

        $ae_select = [
            'asin',
            'available',
            'weight',
            'ae_price',
            'price_updated_at',
        ];

        $sa_csv_header = [
            'Priority',
            'Asin',
            'Available',
            'Weight',
            'Length',
            'Width',
            'Height',
            'Unit',
            'KSA Price',
            'Price Updated At'
        ];

        $sa_select = [
            'asin',
            'available',
            'weight',
            'sa_price',
            'price_updated_at',
        ];

        $total_count = 0;

        $this->export_file_path = "excel/downloads/catalog_price/$this->country_code/$this->priority/CatalogPrice";
        $us_destination  = table_model_create(country_code: $this->country_code, model: 'Asin_destination', table_name: 'asin_destination_');

        $count = $us_destination->max('id');
        $min_id = $us_destination->min('id');

        $total_chunk = ($count / $chunk);

        for ($start = 0; $start <= $total_chunk; $start++) {

            $where_asin = [];
            $asin_priority = [];
            $asin = [];

            $start_id = ($chunk * $start) + $min_id;
            $end_id = (($chunk * ($start + 1))) + $min_id;

            $asin = $us_destination->select('id', 'asin', 'priority')
                ->when($this->priority != 'All', function ($query) {
                    return $query->where('priority', $this->priority);
                })
                ->where('id', '>=', $start_id)
                ->where('id', '<', $end_id)
                ->get();

            $total_count += count($asin);

            if (count($asin) > 0) {

                foreach ($asin as  $value) {
                    $where_asin[$value['id']] = $value['asin'];
                    $asin_priority[$value['asin']] = $value['priority'];
                }

                if ($this->country_code == 'US') {

                    $pricing_details = PricingUs::whereIn('asin', $where_asin)
                        ->get($us_select);

                    $catalog_details = Catalog_us::whereIn('asin', $where_asin)
                        ->get(['asin', 'attributes']);

                    $this->priceDataFormating($pricing_details, $catalog_details, $asin_priority, $us_csv_header, $this->country_code);
                } else if ($this->country_code == 'IN') {

                    $pricing_details = PricingIn::whereIn('asin', $where_asin)
                        ->get($in_select);

                    $catalog_details = Catalog_in::whereIn('asin', $where_asin)
                        ->get(['asin', 'attributes']);

                    $this->priceDataFormating($pricing_details, $catalog_details, $asin_priority, $in_csv_header, $this->country_code);
                } else if ($this->country_code == 'SA') {

                    $pricing_details = PricingSa::whereIn('asin', $where_asin)
                        ->get($sa_select);

                    $catalog_details = Catalog_sa::whereIn('asin', $where_asin)
                        ->get(['asin', 'attributes']);

                    $this->priceDataFormating($pricing_details, $catalog_details, $asin_priority, $sa_csv_header, $this->country_code);
                } else if ($this->country_code == 'AE') {

                    $pricing_details = PricingAe::whereIn('asin', $where_asin)
                        ->get($ae_select);

                    $catalog_details = Catalog_ae::whereIn('asin', $where_asin)
                        ->get(['asin', 'attributes']);

                    $this->priceDataFormating($pricing_details, $catalog_details, $asin_priority, $ae_csv_header, $this->country_code);
                }
            }
        }

        $this->createZip();
        $command_end_time = now();
        // fileManagementUpdate($fm_id, $command_end_time);
    }

    public function priceDataFormating($pricing_details, $catalog_details, $asin_priority, $csv_header, $country_code)
    {
        $records = [];
        $catalog_data = [];

        foreach ($catalog_details as $details) {
            $catalog_data[$details['asin']] = isset($details['attributes']['item_package_dimensions'][0])
                ? $details['attributes']['item_package_dimensions'][0] : (isset($details['attributes']['item_dimensions'][0])
                    ? $details['attributes']['item_dimensions'][0] : 'NA');
        }

        foreach ($pricing_details as $value) {
            $value = $value->toArray();
            $asin = '';
            foreach ($value as $key => $data) {
                if ($key == 'asin' && array_key_exists($data, $asin_priority)) {
                    $records['priority'] = $asin_priority[$data];
                    $records['asin'] = $data;
                    $asin = $data;
                } else {
                    $records[$key] = $data;
                }

                if ($key == 'weight' && array_key_exists($asin, $catalog_data)) {
                    $records['length'] = $this->KeyCheck($catalog_data[$asin], 'length', 'value');
                    $records['width'] = $this->KeyCheck($catalog_data[$asin], 'width', 'value');
                    $records['height'] = $this->KeyCheck($catalog_data[$asin], 'height', 'value');
                    $records['unit'] = $this->KeyCheck($catalog_data[$asin], 'height', 'unit');
                }
            }
            $this->createCsv($csv_header, $records);
        }
        // po($records);
        // exit;
    }

    public function createCsv($csv_header, $records)
    {
        if ($this->count == 1) {
            if (!Storage::exists($this->export_file_path . $this->fileNameOffset . '.csv')) {
                Storage::put($this->export_file_path . $this->fileNameOffset . '.csv', '');
            }
            $this->totalFile[] = "CatalogPrice" . $this->fileNameOffset . '.csv';
            $this->writer = Writer::createFromPath(Storage::path($this->export_file_path . $this->fileNameOffset . '.csv'), "w");
            $this->writer->insertOne($csv_header);
        }

        $this->writer->insertOne($records);

        if ($this->record_per_csv == $this->count) {
            $this->fileNameOffset++;
            $this->count = 1;
        } else {
            ++$this->count;
        }
        return true;
    }

    public function createZip()
    {
        $zip = new ZipArchive;
        $path = "excel/downloads/catalog_price/" . $this->country_code . '/' . $this->priority . '/zip/' . $this->country_code . "_CatalogPrice.zip";
        $file_path = Storage::path($path);
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
            foreach ($this->totalFile as $key => $value) {

                $path = Storage::path('excel/downloads/catalog_price/' . $this->country_code . "/$this->priority/" . $value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }

    public function KeyCheck($data, $key, $type)
    {
        return isset($data[$key][$type]) ? $data[$key][$type] : 'NA';
    }
}
