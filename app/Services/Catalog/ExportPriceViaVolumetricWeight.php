<?php

namespace App\Services\Catalog;

use ZipArchive;
use League\Csv\Writer;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_us;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\Catalog\PriceConversion;
use Exception;

class ExportPriceViaVolumetricWeight
{
    private $countryCode;
    private $priority;
    private $count = 1;
    private $export_file_path;
    private $fileNameOffset = 0;
    private $totalFile = [];
    private $writer;
    private $limit = 10000;
    private $record_per_csv = 1000000;
    private $price_convert;



    public function index($countryCode, $fmID, $priority)
    {
        $this->price_convert = new PriceConversion();
        $this->countryCode = strtoupper($countryCode);
        $this->priority = $priority;

        $this->export_file_path = "excel/downloads/catalog_price/" . $this->countryCode . '/' . $this->priority . "/CatalogPrice";

        $headers_us = [
            'ASIN',
            'AVAILABLE',
            'LENGTH_INCH',
            'WIDTH_INCH',
            'HEIGHT_INCH',
            'WEIGHT_POUND',
            'VOL_POUND',
            'ACTUAL_WEIGHT_POUND',
            'US_PRICE',
            'USATOINB2C',
            'USATOINB2B',
            'USATOAE',
            'USATOSG',
            'UPDATED_AT'
        ];

        $headers_in = [
            'ASIN',
            'AVAILABLE',
            'LENGTH_CM',
            'WIDTH_CM',
            'HEIGHT_CM',
            'WEIGHT_KG',
            'VOL_KG',
            'ACTUAL_WEIGHT_KG',
            'IN_PRICE',
            'INDTOAE',
            'INDTOSG',
            'INDTOSA',
            'UPDATED_AT'
        ];
        $query_limit = 10000;



        $us_destination  = table_model_create(country_code: $this->countryCode, model: 'Asin_destination', table_name: 'asin_destination_');
        $total_asin_count = $us_destination->when($this->priority != 'All', function ($query) {
            return $query->where('priority', $this->priority);
        })
            ->where('export', 0)
            ->orderBy('id', 'asc')
            ->count();

        $total_loop = ceil($total_asin_count / $query_limit);

        for ($i = 0; $i < $total_loop; $i++) {

            $asin = $us_destination->select('asin', 'priority')
                ->when($this->priority != 'All', function ($query) {
                    return $query->where('priority', $this->priority);
                })
                ->where('export', 0)
                ->orderBy('id', 'asc')
                ->limit($query_limit)
                ->get()
                ->toArray();

            $where_asin = [];
            foreach ($asin as $value) {
                $where_asin[] = $value['asin'];
            }


            if ($this->countryCode == 'US') {
                // $start_time = startTime();

                $pricing_details = PricingUs::join("cataloguss", "cataloguss.asin", "pricing_uss.asin")
                    ->select(["cataloguss.length", "cataloguss.width", "cataloguss.height", "cataloguss.weight", "pricing_uss.asin", "pricing_uss.available", "pricing_uss.us_price", "pricing_uss.updated_at"])
                    ->whereIn('pricing_uss.asin', $where_asin)
                    ->get()
                    ->toArray();

                // Log::debug("query" . endTime($start_time));

                // $start_time = startTime();
                $this->dataFormatting($pricing_details, $this->countryCode, $headers_us, $where_asin);
                // Log::debug('data-formatting' . endTime($start_time));
            } elseif ($this->countryCode == 'IN') {

                $pricing_details = PricingIn::join("catalogins", "catalogins.asin", "pricing_ins.asin")
                    ->select(["catalogins.length", "catalogins.width", "catalogins.height", "catalogins.weight", "pricing_ins.asin", "pricing_ins.available", "pricing_ins.in_price", "pricing_ins.updated_at"])
                    ->whereIn('pricing_ins.asin', $where_asin)
                    ->get()
                    ->toArray();

                $this->dataFormatting($pricing_details, $this->countryCode, $headers_in, $where_asin);
            }

            $us_destination->when($this->priority != 'All', function ($query) {
                return $query->where('priority', $this->priority);
            })->whereIn('asin', $where_asin)
                ->update(["export" => 1]);
        }

        $this->createZip($fmID);

        $us_destination->when($this->priority != 'All', function ($query) {
            return $query->where('priority', $this->priority);
        })->where('export', 1)
            ->update(["export" => 0]);
    }

    public function dataFormatting($catalog_details, $countryCode, $headers, $destination_asin)
    {
        $asin_data = [];

        foreach ($catalog_details as $key => $catalog_detail) {
            $weight = 0;
            $height = 0;
            $length = 0;
            $width = 0;
            $packet_dimensions = 0;
            // $dimension = (array) json_decode($catalog_detail['dimensions'], true);

            try {

                // if (array_key_exists('package', $dimension[0]) && gettype($dimension[0]) == 'array') {
                //     if (isset($dimension[0]['package']['weight']['value']) || isset($dimension[0]['package']['height']['value']) || isset($dimension[0]['package']['length']['value']) || isset($dimension[0]['package']['width']['value'])) {

                // $weight = $dimension[0]['package']['weight']['value'] ?? 0;
                // $length = $dimension[0]['package']['length']['value'] ?? 0;
                // $width = $dimension[0]['package']['width']['value'] ?? 0;
                // $height = $dimension[0]['package']['height']['value'] ?? 0;

                $weight = $catalog_detail['weight'];
                $length = $catalog_detail['length'];
                $width  = $catalog_detail['width'];
                $height = $catalog_detail['height'];

                $asin_data[$key]['ASIN'] = $catalog_detail['asin'];
                $packet_dimensions = $height * $length * $width;

                if ($countryCode == 'IN') {
                    if (isset($catalog_detail['in_price']) && gettype($catalog_detail) == "array") {

                        $poundToKg = poundToKg($weight);
                        $volKg = VolumetricIntoKG($packet_dimensions);
                        $actual_weight_kg = $poundToKg > $volKg ? $poundToKg : $volKg;


                        $in_price = $catalog_detail['in_price'] ?? 0;
                        $asin_data[$key]['AVAILABLE'] = $in_price != 0 ? $catalog_detail['available'] : 0;
                        $asin_data[$key]['LENGHT_CM'] = $length * 2.54;         //inch to cm
                        $asin_data[$key]['WIDTH_CM']  = $width * 2.54;          //inch to cm
                        $asin_data[$key]['HEIGHT_CM'] = $height * 2.54;         //inch to cm
                        $asin_data[$key]['WEIGHT_KG'] = $poundToKg;
                        $asin_data[$key]['VOL_KG']    = $volKg;
                        $asin_data[$key]['ACTUAL_WEIGHT_KG'] = $actual_weight_kg;
                        $asin_data[$key]['IN_PRICE'] = $in_price;

                        $convertedPrice = $this->priceConversion($actual_weight_kg, $in_price, $countryCode);
                        foreach ($convertedPrice as $key2 => $price) {
                            $asin_data[$key][$key2] = $price;
                        }
                        $asin_data[$key]['UPDATED_AT'] = date("Y-m-d H:i:s", strtotime($catalog_detail['updated_at']));
                    }
                } elseif ($countryCode == 'US') {

                    if (isset($catalog_detail['us_price']) && gettype($catalog_detail) == "array") {

                        $volPound = VolumetricIntoPounds($packet_dimensions);
                        $actual_weight_pound = $weight > $volPound ? $weight : $volPound;

                        $us_price = $catalog_detail['us_price'] ?? 0;
                        $asin_data[$key]['AVAILABLE'] = $us_price != 0 ? $catalog_detail['available'] : 0;
                        $asin_data[$key]['LENGTH_INCH'] = $length;
                        $asin_data[$key]['WIDTH_INCH'] = $width;
                        $asin_data[$key]['HEIGHT_INCH'] = $height;
                        $asin_data[$key]['WEIGHT_POUND'] = $weight;
                        $asin_data[$key]['VOL_POUND'] = $volPound;
                        $asin_data[$key]['ACTUAL_VOL_POUND'] = $actual_weight_pound;
                        $asin_data[$key]['US_PRICE'] = $us_price;

                        // $start_time = startTime();
                        $packetPrice = $this->priceConversion($actual_weight_pound, $us_price, $countryCode);
                        // Log::debug('price-conversion' . endTime($start_time));
                        foreach ($packetPrice as $key2 => $price) {
                            $asin_data[$key][$key2] = $price;
                        }
                        $asin_data[$key]['UPDATED_AT'] = date("Y-m-d H:i:s", strtotime($catalog_detail['updated_at']));
                    }
                }
                //     }
                // }
            } catch (Exception $e) {
            }
        }
        $asin = [];
        $unavailable_asin = [];

        foreach ($asin_data as $catalog_data) {
            $asin[] = $catalog_data['ASIN'];
        }

        $data = array_diff($destination_asin, $asin);

        foreach ($data as  $desti_asin) {

            $unavailable_asin[] = [
                'ASIN' => $desti_asin,
                'AVAILABLE' => 'NULL',
                'LENGHT_CM' => 'NULL',
                'WIDTH_CM' => 'NULL',
                'HEIGHT_CM' => 'NULL',
                'WEIGHT_KG' => 'NULL',
                'VOL_KG' => 'NULL',
                'ACTUAL_WEIGHT_KG' => 'NULL',
                'IN_PRICE' => 'NULL',
                'INDTOAE' => 'NULL',
                'INDTOSG' => 'NULL',
                'INDTOSA' => 'NULL',
                'UPDATED_AT' => 'NULL',
            ];
        }
        $data_for_csv = [...$asin_data, ...$unavailable_asin];
        Log::debug($data_for_csv);
        // $start_time = startTime();
        $this->createCsv($headers, $data_for_csv);
        // Log::debug('csv-import' . endTime($start_time));
        $asin_data = [];
        return true;
    }

    public function priceConversion($weight, $bbPrice, $countryCode)
    {
        $pricing = [];
        if ($countryCode == 'US') {

            if ($bbPrice != 0) {

                $price_in_b2c = $this->price_convert->USAToINDB2C($weight, $bbPrice);
                $price_in_b2b = $this->price_convert->USAToINDB2B($weight, $bbPrice);
                $price_ae = $this->price_convert->USATOUAE($weight, $bbPrice);
                $price_sg =  $this->price_convert->USATOSG($weight, $bbPrice);
                $pricing = [
                    'USATOINB2C' => $price_in_b2c,
                    'USATOINB2B' => $price_in_b2b,
                    'USATOAE' => $price_ae,
                    'USATOSG' => $price_sg
                ];
            } else {
                $pricing = [
                    'USATOINB2C' => 0,
                    'USATOINB2B' => 0,
                    'USATOAE' => 0,
                    'USATOSG' => 0
                ];
            }
        } else if ($countryCode == 'IN') {
            if ($bbPrice != 0) {

                $price_uae = $this->price_convert->INDToUAE($weight, $bbPrice);
                $price_singapore = $this->price_convert->INDToSG($weight, $bbPrice);
                $price_saudi = $this->price_convert->INDToSA($weight, $bbPrice);
                $pricing = [
                    'INDTOAE' => $price_uae,
                    'INDTOSG' => $price_singapore,
                    'INDTOSA' => $price_saudi
                ];
            } else {
                $pricing = [
                    'INDTOAE' => 0,
                    'INDTOSG' => 0,
                    'INDTOSA' => 0
                ];
            }
        }
        return $pricing;
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
        // $start_time = startTime();
        $this->writer->insertAll($records);
        // Log::debug('data-into-csv' . endTime($start_time));

        $remender = $this->record_per_csv / $this->limit;
        if ($remender == $this->count) {
            $this->fileNameOffset++;
            $this->count = 1;
        } else {
            ++$this->count;
        }

        return true;
    }

    public function createZip($fmID)
    {
        $zip = new ZipArchive;
        $path = "excel/downloads/catalog_price/" . $this->countryCode . '/' . $this->priority . '/zip/' . $this->countryCode . "_CatalogPrice.zip";
        $file_path = Storage::path($path);
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
            foreach ($this->totalFile as $key => $value) {

                $path = Storage::path('excel/downloads/catalog_price/' . $this->countryCode . "/$this->priority/" . $value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
        Log::alert('zip-created');
        $command_end_time = now();
        fileManagementUpdate($fmID, $command_end_time);
    }
}
