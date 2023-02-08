<?php

use League\Csv\Writer;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_us;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\Catalog\PriceConversion;

Route::get('volume', function () {

    $catalogTable = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');
    $catalogRecords = $catalogTable->select('asin', 'height', 'length', 'width', 'unit', 'weight')
        ->get()
        ->toArray();

    // po($catalogRecords);
    foreach ($catalogRecords as $catalogRecord) {
        po($catalogRecord);
        $dimension = $catalogRecord['height'] * $catalogRecord['length'] * $catalogRecord['width'];
        $volumetricOfKg = VolumetricIntoKG($dimension);
        $volumetricOfPounds = VolumetricIntoPounds($dimension);
        po($volumetricOfKg);
        po($volumetricOfPounds);
    }
});

Route::get('export', function () {

    $headers = [
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
        'vol_weight_kg',
        'vol_kg_USATOINB2C',
        'vol_kg_USATOINB2B',
        'vol_kg_USATOAE',
        'vol_kg_USATOSG'
    ];
    $countryCode = 'IN';
    $priority = 2;

    $us_destination  = table_model_create(country_code: $countryCode, model: 'Asin_destination', table_name: 'asin_destination_');
    $us_destination->select('asin', 'priority')
        ->when($priority != 'All', function ($query) use ($priority) {
            return $query->where('priority', $priority);
        })
        ->where('export', '0')
        ->chunk(100, function ($asin) use ($countryCode, $priority, $headers) {
            $asin = $asin->toArray();
            $where_asin = [];
            $data = [];

            foreach ($asin as $value) {
                $where_asin[] = $value['asin'];
            }
            if ($countryCode == 'US') {

                $pricing_details = PricingUs::with(['catalogUS'])->whereIn('asin', $where_asin)->get(['asin', 'us_price'])->toArray();
                po($pricing_details);
                exit;
                $catalog_details = Catalog_us::whereIn('asin', $where_asin)->get(['asin', 'dimensions'])->toArray();

                foreach ($catalog_details as $key1 => $catalog) {
                    $data[] = [...$catalog, ...$pricing_details[$key1] ?? ''];
                }
                // dataFormatting($data, $countryCode, $priority, $headers);
            } elseif ($countryCode == 'IN') {

                $pricing_details = PricingIn::join("catalognewins", "catalognewins.asin", "pricing_ins.asin")
                    ->select(["catalognewins.dimensions", "pricing_ins.asin", "pricing_ins.in_price"])
                    ->whereIn('pricing_ins.asin', $where_asin)
                    ->get()
                    ->toArray();
                foreach ($pricing_details as $details) {
                    $data[] = $details;
                }
                // dataFormatting($data, $countryCode, $priority, $headers);
            }
        });
});

// function dataFormatting($catalog_details, $countryCode, $priority, $headers)
// {
//     $asin_data = [];
//     foreach ($catalog_details as $key1 => $catalog_detail) {

//         $dimension = json_decode($catalog_detail['dimensions'], true);

//         if (array_key_exists('package', $dimension[0])) {
//             if (isset($dimension[0]['package']['weight']['value']) || isset($dimension[0]['package']['height']['value']) || isset($dimension[0]['package']['length']['value']) || isset($dimension[0]['package']['width']['value'])) {
//                 // po($dimension[0]['package']);
//                 $weight = $dimension[0]['package']['weight']['value'] ?? 0;
//                 $height = $dimension[0]['package']['height']['value'] ?? 0;
//                 $length = $dimension[0]['package']['length']['value'] ?? 0;
//                 $width = $dimension[0]['package']['width']['value'] ?? 0;

//                 $asin_data[$key1]['height'] = $height;
//                 $asin_data[$key1]['length'] = $length;
//                 $asin_data[$key1]['width'] = $width;
//                 $asin_data[$key1]['unit'] = ($dimension[0]['package']['width']['unit']) ?? 'inches';
//                 $asin_data[$key1]['weight'] = $weight;
//                 $asin_data[$key1]['weight_unit'] = ($dimension[0]['package']['weight']['unit']) ?? 'inches';
//             }
//             if ($countryCode == 'IN') {

//                 $in_price = $catalog_detail['in_price'] ?? 0;
//                 $asin_data[$key1]['price'] = $in_price;
//                 $packetPrice = priceConversion($weight, $in_price, $countryCode, 'packet');
//                 foreach ($packetPrice as $key2 => $price) {
//                     $asin_data[$key1][$key2] = $price;
//                 }
//             } elseif ($countryCode == 'US') {

//                 $us_price = $catalog_detail['us_price'] ?? 0;
//                 $asin_data[$key1]['price'] = $us_price;
//                 $packetPrice = priceConversion($weight, $us_price, $countryCode, 'packet');
//                 foreach ($packetPrice as $key2 => $price) {
//                     $asin_data[$key1][$key2] = $price;
//                 }
//             }
//         }
//     }
//     po($asin_data);
// }

// function priceConversion($weight, $bbPrice, $countryCode, $type)
// {
//     $price_convert = new PriceConversion();
//     // $pricing = [];
//     if ($countryCode == 'US') {

//         $price_in_b2c = $price_convert->USAToINDB2C($weight, $bbPrice) ?? 'NA';
//         $price_in_b2b = $price_convert->USAToINDB2B($weight, $bbPrice) ?? 'NA';
//         $price_ae = $price_convert->USATOUAE($weight, $bbPrice) ?? 'NA';
//         $price_sg =  $price_convert->USATOSG($weight, $bbPrice) ?? 'NA';
//         $pricing = [
//             $type . '_USATOINB2C' => $price_in_b2c,
//             $type . '_USATOINB2B' => $price_in_b2b,
//             $type . '_USATOAE' => $price_ae,
//             $type . '_USATOSG' => $price_sg
//         ];
//     } else if ($countryCode == 'IN') {

//         $packet_weight_kg = poundToKg($weight);
//         $price_uae = $price_convert->INDToUAE($packet_weight_kg, $bbPrice) ?? 'NA';
//         $price_singapore = $price_convert->INDToSG($packet_weight_kg, $bbPrice) ?? 'NA';
//         $price_saudi = $price_convert->INDToSA($packet_weight_kg, $bbPrice) ?? 'NA';
//         $pricing = [
//             $type . '_weight_kg' => $packet_weight_kg,
//             $type . '_INDTOAE' => $price_uae,
//             $type . '_INDTOSG' => $price_singapore,
//             $type . '_INDTOSA' => $price_saudi
//         ];
//     }

//     return $pricing;
// }
