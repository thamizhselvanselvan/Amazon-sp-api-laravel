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

class ExportPriceViaVolumetricWeight
{
    private $countryCode;
    private $priority;
    private $count = 1;
    private $export_file_path;
    private $fileNameOffset = 0;
    private $totalFile = [];
    private $writer;
    private $limit = 1000;
    private $record_per_csv = 1000000;

    public function index($countryCode, $fmID, $priority)
    {
        $this->countryCode = strtoupper($countryCode);
        $this->priority = $priority;
        $id = Auth::user()->id;
        $this->export_file_path = "excel/downloads/catalog_price/" . $this->countryCode . '/' . $this->priority . "/CatalogPrice";

        $headers_us = [
            'asin',
            'height',
            'length',
            'width',
            'unit',
            'weight',
            'weight_unit',
            'us_price',
            'packet_USATOINB2C',
            'packet_USATOINB2B',
            'packet_USATOAE',
            'packet_USATOSG',
            'vol_weight_pounds',
            'vol_pound_USATOINB2C',
            'vol_pound_USATOINB2B',
            'vol_pound_USATOAE',
            'vol_pound_USATOSG',
            'vol_kg_weight',
            'vol_kg_USATOINB2C',
            'vol_kg_USATOINB2B',
            'vol_kg_USATOAE',
            'vol_kg_USATOSG'
        ];

        $headers_in = [
            'asin',
            'height',
            'length',
            'width',
            'unit',
            'weight',
            'weight_unit',
            'in_price',
            'packet_kg',
            'packet_INDTOAE',
            'packet_INDTOSG',
            'packet_INDTOSA',
            'vol_kg',
            'vol_kg_INDTOAE',
            'vol_kg_INDTOSG',
            'vol_kg_INDTOSA',
            'vol_pound',
            'vol_pound_INDTOAE',
            'vol_pound_INDTOSG',
            'vol_pound_INDTOSA'
        ];
        query1:
        $us_destination  = table_model_create(country_code: $this->countryCode, model: 'Asin_destination', table_name: 'asin_destination_');
        $asin = $us_destination->select('asin', 'priority')
            ->when($this->priority != 'All', function ($query) {
                return $query->where('priority', $this->priority);
            })
            ->where('export', '0')
            ->limit($this->limit)
            ->get()
            ->toArray();
        $where_asin = [];
        $upsert_data = [];
        foreach ($asin as $value) {
            $where_asin[] = $value['asin'];

            $upsert_data[] = [
                'asin' => $value['asin'],
                'user_id' => $id,
                'export' => '1',
            ];
        }
        $us_destination->upsert($upsert_data, ['user_asin_unique'], ['asin', 'export']);
        $upsert_data = [];
        $data = [];
        if ($this->countryCode == 'US') {
            $pricing_details = PricingUs::whereIn('asin', $where_asin)->get(['asin', 'us_price'])->toArray();
            $catalog_details = Catalog_us::whereIn('asin', $where_asin)->get(['asin', 'dimensions'])->toArray();
            foreach ($catalog_details as $key1 => $catalog) {
                $data[] = [...$catalog, ...$pricing_details[$key1]];
            }
            $this->dataFormatting($data, $this->countryCode, $headers_us);
            $where_asin = [];
        } elseif ($this->countryCode == 'IN') {

            $pricing_details = PricingIn::whereIn('asin', $where_asin)->get(['asin', 'in_price'])->toArray();
            $catalog_details = Catalog_in::whereIn('asin', $where_asin)->get(['asin', 'dimensions'])->toArray();
            foreach ($catalog_details as $key1 => $catalog) {
                $data[] = [...$catalog, ...$pricing_details[$key1]];
            }
            $this->dataFormatting($data, $this->countryCode, $headers_in);
            $where_asin = [];
        }
        $data = $us_destination->where('export', '0')->get()->count('id');
        if ($data != 0) {
            goto query1;
        } else {
            $this->createZip($fmID);
        }
    }

    public function dataFormatting($catalog_details, $countryCode, $headers)
    {
        $asin_data = [];
        foreach ($catalog_details as $key => $catalog_detail) {
            $weight = 0;
            $dimensions = 0;
            if (isset($catalog_detail['dimensions'][0]) && array_key_exists('package', $catalog_detail['dimensions'][0])) {
                if (isset($catalog_detail['dimensions'][0]['package']['weight']['value'])) {

                    $height = ($catalog_detail['dimensions'][0]['package']['height']['value']);
                    $length = ($catalog_detail['dimensions'][0]['package']['length']['value']);
                    $width = ($catalog_detail['dimensions'][0]['package']['width']['value']);
                    $weight = ($catalog_detail['dimensions'][0]['package']['weight']['value']);

                    $asin_data[$key]['asin'] = $catalog_detail['asin'];
                    $asin_data[$key]['height'] = $height;
                    $asin_data[$key]['length'] = $length;
                    $asin_data[$key]['width'] = $width;
                    $asin_data[$key]['unit'] = ($catalog_detail['dimensions'][0]['package']['width']['unit']);
                    $asin_data[$key]['weight'] = $weight;
                    $asin_data[$key]['weight_unit'] = ($catalog_detail['dimensions'][0]['package']['weight']['unit']);

                    $dimensions = $height * $length * $width;
                }
            }
            if ($countryCode == 'IN') {
                if (isset($catalog_detail['in_price'])) {

                    $in_price = $catalog_detail['in_price'];
                    $asin_data[$key]['in_price'] = $in_price;
                    $packetPrice = $this->priceConversion($weight, $in_price, $countryCode, 'packet');
                    foreach ($packetPrice as $key2 => $price) {
                        $asin_data[$key][$key2] = $price;
                    }

                    $vol_kg = VolumetricIntoKG($dimensions);
                    $asin_data[$key]['vol_kg'] = $vol_kg;
                    $packetPrice = $this->priceConversion($vol_kg, $in_price, $countryCode, 'vol_kg');
                    foreach ($packetPrice as $key3 => $price) {
                        $asin_data[$key][$key3] = $price;
                    }

                    $vol_pounds = VolumetricIntoPounds($dimensions);
                    $asin_data[$key]['vol_pound'] = $vol_pounds;
                    $packetPrice = $this->priceConversion($vol_pounds, $in_price, $countryCode, 'vol_pound');
                    foreach ($packetPrice as $key4 => $price) {
                        $asin_data[$key][$key4] = $price;
                    }
                }
            } elseif ($countryCode == 'US') {
                if (isset($catalog_detail['us_price'])) {

                    $us_price = $catalog_detail['us_price'];
                    $asin_data[$key]['us_price'] = $us_price;
                    $packetPrice = $this->priceConversion($weight, $us_price, $countryCode, 'packet');
                    foreach ($packetPrice as $key2 => $price) {
                        $asin_data[$key][$key2] = $price;
                    }

                    $vol_pounds = VolumetricIntoPounds($dimensions);
                    $asin_data[$key]['vol_weight_pounds'] = $vol_pounds;
                    $packetPrice = $this->priceConversion($vol_pounds, $us_price, $countryCode, 'vol_pound');
                    foreach ($packetPrice as $key3 => $price) {
                        $asin_data[$key][$key3] = $price;
                    }

                    $vol_kg = VolumetricIntoKG($dimensions);
                    $asin_data[$key]['vol_weight_kg'] = $vol_kg;
                    $packetPrice = $this->priceConversion($vol_kg, $us_price, $countryCode, 'vol_kg');
                    foreach ($packetPrice as $key4 => $price) {
                        $asin_data[$key][$key4] = $price;
                    }
                }
            }
        }
        Log::alert($asin_data);
        $this->createCsv($headers, $asin_data);
        $asin_data = [];
        return true;
    }

    public function priceConversion($weight, $bbPrice, $countryCode, $type)
    {
        $pricing = [];
        $price_convert = new PriceConversion();
        if ($countryCode == 'US') {

            $price_in_b2c = $price_convert->USAToINDB2C($weight, $bbPrice);
            $price_in_b2b = $price_convert->USAToINDB2B($weight, $bbPrice);
            $price_ae = $price_convert->USATOUAE($weight, $bbPrice);
            $price_sg =  $price_convert->USATOSG($weight, $bbPrice);
            $pricing = [
                $type . '_USATOINB2C' => $price_in_b2c,
                $type . '_USATOINB2B' => $price_in_b2b,
                $type . '_USATOAE' => $price_ae,
                $type . '_USATOSG' => $price_sg
            ];
        } else if ($countryCode == 'IN') {
            if ($type == 'packet') {

                $packet_weight_kg = poundToKg($weight);
                $price_uae = $price_convert->INDToUAE($packet_weight_kg, $bbPrice);
                $price_singapore = $price_convert->INDToSG($packet_weight_kg, $bbPrice);
                $price_saudi = $price_convert->INDToSA($packet_weight_kg, $bbPrice);
                $pricing = [
                    $type . '_kg' => $packet_weight_kg,
                    $type . '_INDTOAE' => $price_uae,
                    $type . '_INDTOSG' => $price_singapore,
                    $type . '_INDTOSA' => $price_saudi
                ];
            } else {
                $price_uae = $price_convert->INDToUAE($weight, $bbPrice);
                $price_singapore = $price_convert->INDToSG($weight, $bbPrice);
                $price_saudi = $price_convert->INDToSA($weight, $bbPrice);
                $pricing = [
                    $type . '_INDTOAE' => $price_uae,
                    $type . '_INDTOSG' => $price_singapore,
                    $type . '_INDTOSA' => $price_saudi
                ];
            }
        }
        log::alert($pricing);
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
        foreach ($records as $record) {
            $this->writer->insertOne($record);
        }
        Log::alert($records);
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
        $command_end_time = now();
        fileManagementUpdate($fmID, $command_end_time);
    }
}
