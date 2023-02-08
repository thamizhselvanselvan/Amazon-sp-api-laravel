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
    private $limit = 5000;
    private $record_per_csv = 1000000;
    private $price_convert;



    public function index($countryCode, $fmID, $priority)
    {
        $this->price_convert = new PriceConversion();
        $this->countryCode = strtoupper($countryCode);
        $this->priority = $priority;

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
        $query_limit = 5000;



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

                $pricing_details = PricingUs::join("catalognewuss", "catalognewuss.asin", "pricing_uss.asin")
                    ->select(["catalognewuss.dimensions", "pricing_uss.asin", "pricing_uss.us_price"])
                    ->whereIn('pricing_uss.asin', $where_asin)
                    ->get()
                    ->toArray();


                $this->dataFormatting($pricing_details, $this->countryCode, $headers_us);
            } elseif ($this->countryCode == 'IN') {

                $pricing_details = PricingIn::join("catalognewins", "catalognewins.asin", "pricing_ins.asin")
                    ->select(["catalognewins.dimensions", "pricing_ins.asin", "pricing_ins.in_price"])
                    ->whereIn('pricing_ins.asin', $where_asin)
                    ->get()
                    ->toArray();

                $this->dataFormatting($pricing_details, $this->countryCode, $headers_in);
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

    public function dataFormatting($catalog_details, $countryCode, $headers)
    {
        $asin_data = [];

        foreach ($catalog_details as $key => $catalog_detail) {
            $weight = 0;
            $height = 0;
            $length = 0;
            $width = 0;
            $packet_dimensions = 0;
            $dimension = (array) json_decode($catalog_detail['dimensions'], true);

            if (array_key_exists('package', $dimension[0]) && gettype($dimension[0]) == 'array') {
                if (isset($dimension[0]['package']['weight']['value']) || isset($dimension[0]['package']['height']['value']) || isset($dimension[0]['package']['length']['value']) || isset($dimension[0]['package']['width']['value'])) {

                    $weight = $dimension[0]['package']['weight']['value'] ?? 0;
                    $height = $dimension[0]['package']['height']['value'] ?? 0;
                    $length = $dimension[0]['package']['length']['value'] ?? 0;
                    $width = $dimension[0]['package']['width']['value'] ?? 0;
                    $asin_data[$key]['asin'] = $catalog_detail['asin'];
                    $asin_data[$key]['height'] = $height;
                    $asin_data[$key]['length'] = $length;
                    $asin_data[$key]['width'] = $width;
                    $asin_data[$key]['unit'] = ($dimension[0]['package']['width']['unit']) ?? 'inches';
                    $asin_data[$key]['weight'] = $weight;
                    $asin_data[$key]['weight_unit'] = ($dimension[0]['package']['weight']['unit']) ?? 'pounds';
                    $packet_dimensions = $height * $length * $width;

                    if ($countryCode == 'IN') {
                        if (isset($catalog_detail['in_price']) && gettype($catalog_detail) == "array") {

                            $in_price = $catalog_detail['in_price'] ?? 0;
                            $asin_data[$key]['price'] = $in_price;
                            $packetPrice = $this->priceConversion($weight, $in_price, $countryCode, 'packet');
                            foreach ($packetPrice as $key2 => $price) {
                                $asin_data[$key][$key2] = $price;
                            }

                            $vol_kg = VolumetricIntoKG($packet_dimensions);
                            $asin_data[$key]['vol_kg'] = $vol_kg;
                            $packetPrice = $this->priceConversion($vol_kg, $in_price, $countryCode, 'vol_kg');
                            foreach ($packetPrice as $key3 => $price) {
                                $asin_data[$key][$key3] = $price;
                            }

                            $vol_pounds = VolumetricIntoPounds($packet_dimensions);
                            $asin_data[$key]['vol_pound'] = $vol_pounds;
                            $packetPrice = $this->priceConversion($vol_pounds, $in_price, $countryCode, 'vol_pound');
                            foreach ($packetPrice as $key4 => $price) {
                                $asin_data[$key][$key4] = $price;
                            }
                        }
                    } elseif ($countryCode == 'US') {
                        if (isset($catalog_detail['us_price']) && gettype($catalog_detail) == "array") {

                            $us_price = $catalog_detail['us_price'] ?? 0;
                            $asin_data[$key]['price'] = $us_price;
                            $packetPrice = $this->priceConversion($weight, $us_price, $countryCode, 'packet');
                            foreach ($packetPrice as $key2 => $price) {
                                $asin_data[$key][$key2] = $price;
                            }

                            $vol_pounds = VolumetricIntoPounds($packet_dimensions);
                            $asin_data[$key]['vol_weight_pounds'] = $vol_pounds;
                            $packetPrice = $this->priceConversion($vol_pounds, $us_price, $countryCode, 'vol_pound');
                            foreach ($packetPrice as $key3 => $price) {
                                $asin_data[$key][$key3] = $price;
                            }

                            $vol_kg = VolumetricIntoKG($packet_dimensions);
                            $asin_data[$key]['vol_weight_kg'] = $vol_kg;
                            $packetPrice = $this->priceConversion($vol_kg, $us_price, $countryCode, 'vol_kg');
                            foreach ($packetPrice as $key4 => $price) {
                                $asin_data[$key][$key4] = $price;
                            }
                        }
                    }
                }
            }
        }

        $this->createCsv($headers, $asin_data);
        $asin_data = [];
        return true;
    }

    public function priceConversion($weight, $bbPrice, $countryCode, $type)
    {
        $pricing = [];

        if ($countryCode == 'US') {

            $price_in_b2c = $this->price_convert->USAToINDB2C($weight, $bbPrice);
            $price_in_b2b = $this->price_convert->USAToINDB2B($weight, $bbPrice);
            $price_ae = $this->price_convert->USATOUAE($weight, $bbPrice);
            $price_sg =  $this->price_convert->USATOSG($weight, $bbPrice);
            $pricing = [
                $type . '_USATOINB2C' => $price_in_b2c,
                $type . '_USATOINB2B' => $price_in_b2b,
                $type . '_USATOAE' => $price_ae,
                $type . '_USATOSG' => $price_sg
            ];
        } else if ($countryCode == 'IN') {
            if ($type == 'packet') {

                $packet_weight_kg = poundToKg($weight);
                $price_uae = $this->price_convert->INDToUAE($packet_weight_kg, $bbPrice);
                $price_singapore = $this->price_convert->INDToSG($packet_weight_kg, $bbPrice);
                $price_saudi = $this->price_convert->INDToSA($packet_weight_kg, $bbPrice);
                $pricing = [
                    $type . '_kg' => $packet_weight_kg,
                    $type . '_INDTOAE' => $price_uae,
                    $type . '_INDTOSG' => $price_singapore,
                    $type . '_INDTOSA' => $price_saudi
                ];
            } else {
                $price_uae = $this->price_convert->INDToUAE($weight, $bbPrice);
                $price_singapore = $this->price_convert->INDToSG($weight, $bbPrice);
                $price_saudi = $this->price_convert->INDToSA($weight, $bbPrice);
                $pricing = [
                    $type . '_INDTOAE' => $price_uae,
                    $type . '_INDTOSG' => $price_singapore,
                    $type . '_INDTOSA' => $price_saudi
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

        $this->writer->insertAll($records);

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
