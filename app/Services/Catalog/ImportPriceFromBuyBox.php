<?php

namespace App\Services\Catalog;

use Carbon\Carbon;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Catalog\PriceConversion;


class ImportPriceFromBuyBox
{
    public function GetPriceFromBuyBox($country_code)
    {
        $priorityArray = ['P1' => 1, 'P2' => 2, 'P3' => 3];
        $price_convert = new PriceConversion();

        foreach ($priorityArray as $priority) {

            $subMinutes = getSystemSettingsValue('fetch_buybox_of_last_minutes', 5);
            $start = "'" . Carbon::now()->subMinutes($subMinutes)->toDateTimeString() . "'";
            $end = "'" . Carbon::now()->toDateTimeString() . "'";

            $country_code_lr = strtolower($country_code);
            $product_seller_details = "bb_product_aa_custom_p${priority}_${country_code_lr}_seller_details";
            $product_lp = "bb_product_aa_custom_p${priority}_${country_code_lr}_offers";

            $BuyBoxRecords = DB::connection('buybox')
                ->select("SELECT PPO.asin, LP.available, 
                            LP.is_sold_by_amazon,
                            LP.is_any_our_seller_own_bb, 
                            LP.next_highest_seller_price,
                            LP.next_highest_seller_id,
                            LP.next_lowest_seller_price,
                            LP.next_lowest_seller_id,
                            LP.bb_winner_price,
                            LP.bb_winner_id,
                            LP.updated_at as updated_at,
                                GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                                group_concat(PPO.listingprice_amount) as listingprice_amount
                                FROM $product_seller_details as PPO
                                JOIN $product_lp as LP 
                                ON PPO.asin = LP.asin
                                WHERE LP.updated_at BETWEEN $start AND $end 
                                GROUP BY PPO.asin
                            ");
            $count = 0;
            $asins = [];
            $Records = [];
            $catalogRecords = [];
            // Log::notice($country_code_lr . '=>' . count($BuyBoxRecords));
            $catalogTable = table_model_create(country_code: $country_code_lr, model: 'Catalog', table_name: 'catalognew');
            foreach ($BuyBoxRecords as $BuyBoxRecord) {

                $Records[$BuyBoxRecord->asin] = $BuyBoxRecord;
                $asins[] = $BuyBoxRecord->asin;

                if ($count == 1000) {
                    $catalogRecords[] = $catalogTable->select('asin', 'dimensions')
                        ->whereIn('asin', $asins)
                        ->get()
                        ->toArray();

                    $count = 0;
                    $asins = [];
                }
                $count++;
            }
            $catalogRecords[] = $catalogTable->select('asin', 'dimensions')
                ->whereIn('asin', $asins)
                ->get()
                ->toArray();


            $BBRecords = [];
            $catalogWeight = [];
            $asinDetails = [];
            $weight = 0;
            $height = 0;
            $length = 0;
            $width = 0;

            foreach ($catalogRecords as $catalogRecord) {
                foreach ($catalogRecord as $catalog) {
                    $weight = '0.5';
                    $BBRecords[] = $Records[$catalog['asin']];

                    if (isset(json_decode($catalog['dimensions'])[0]->package->weight->value)) {
                        $weight = json_decode($catalog['dimensions'])[0]->package->weight->value;
                    }

                    if (isset(json_decode($catalog['dimensions'])[0]->package->height->value)) {
                        $height = json_decode($catalog['dimensions'])[0]->package->height->value;
                    }

                    if (isset(json_decode($catalog['dimensions'])[0]->package->length->value)) {
                        $length = json_decode($catalog['dimensions'])[0]->package->length->value;
                    }

                    if (isset(json_decode($catalog['dimensions'])[0]->package->width->value)) {
                        $width = json_decode($catalog['dimensions'])[0]->package->width->value;
                    }

                    $catalogWeight[$catalog['asin']]['weight'] = $weight;
                    $catalogWeight[$catalog['asin']]['height'] = $height;
                    $catalogWeight[$catalog['asin']]['length'] = $length;
                    $catalogWeight[$catalog['asin']]['width'] = $width;
                }
            }
            // Log::notice($catalogWeight);
            $BBlistingPrice = '';
            $pricing_in = [];
            $pricing_us = [];
            // Log::notice($country_code_lr . '=>' . count($BBRecords));
            $count1 = 0;
            foreach ($BBRecords as $BBRecord) {

                $asin = $BBRecord->asin;
                $packet_weight = $catalogWeight[$asin]['weight'] ?? 0;
                $packet_height = $catalogWeight[$asin]['height'] ?? 0;
                $packet_length = $catalogWeight[$asin]['length'] ?? 0;
                $packet_width  = $catalogWeight[$asin]['width'] ?? 0;
                $dimension = $packet_height * $packet_length * $packet_width;


                $available = $BBRecord->available;
                $is_sold_by_amazon = $BBRecord->is_sold_by_amazon;
                $is_our_seller_bb_winner = $BBRecord->is_any_our_seller_own_bb;
                $next_highest_seller_price = $BBRecord->next_highest_seller_price;
                $next_highest_seller_id = $BBRecord->next_highest_seller_id;
                $next_lowest_seller_price = $BBRecord->next_lowest_seller_price;
                $next_lowest_seller_id = $BBRecord->next_lowest_seller_id;
                $bb_winner_price = $BBRecord->bb_winner_price;
                $bb_winner_id = $BBRecord->bb_winner_id;
                $updated_at = $BBRecord->updated_at;

                $isBuyBoxWinner = explode(',', $BBRecord->is_buybox_winner);
                $listingAmount = explode(',', $BBRecord->listingprice_amount);

                $volumetricPounds = VolumetricIntoPounds($dimension);
                $volumetricKg = VolumetricIntoKG($dimension);

                foreach ($isBuyBoxWinner as $key1 => $BuyBoxWinner) {
                    $price = $country_code_lr . '_price';

                    if ($BuyBoxWinner == 1) {

                        $BBlistingPrice = $listingAmount[$key1] ?? 0;

                        $asinDetails = [
                            'asin'                      => $asin,
                            'available'                 => $available,
                            'is_sold_by_amazon'         => $is_sold_by_amazon,
                            $price                      => $BBlistingPrice,
                            'next_highest_seller_price' => $next_highest_seller_price,
                            'next_highest_seller_id'    => $next_highest_seller_id,
                            'next_lowest_seller_price'  => $next_lowest_seller_price,
                            'next_lowest_seller_id'     => $next_lowest_seller_id,
                            'bb_winner_price'           => $bb_winner_price,
                            'bb_winner_id'              => $bb_winner_id,
                            'is_any_our_seller_won_bb'  => $is_our_seller_bb_winner,
                            'price_updated_at'          => $updated_at,
                        ];

                        break 1;
                    } else {
                        $BBlistingPrice = min($listingAmount);

                        $asinDetails = [
                            'asin'                      => $asin,
                            'available'                 => $available,
                            'is_sold_by_amazon'         => $is_sold_by_amazon,
                            $price                      => $BBlistingPrice,
                            'next_highest_seller_price' => $next_highest_seller_price,
                            'next_highest_seller_id'    => $next_highest_seller_id,
                            'next_lowest_seller_price'  => $next_lowest_seller_price,
                            'next_lowest_seller_id'     => $next_lowest_seller_id,
                            'bb_winner_price'           => $bb_winner_price,
                            'bb_winner_id'              => $bb_winner_id,
                            'is_any_our_seller_won_bb'  => $is_our_seller_bb_winner,
                            'price_updated_at'          => $updated_at,
                        ];
                    }
                }
                if ($country_code_lr == 'us') {
                    $vol_packet_weight = $volumetricPounds > $packet_weight ? $volumetricPounds : $packet_weight;
                    $price_in_b2c = $price_convert->USAToINDB2C($vol_packet_weight, $BBlistingPrice);
                    $price_in_b2b = $price_convert->USAToINDB2B($vol_packet_weight, $BBlistingPrice);
                    $price_ae = $price_convert->USATOUAE($vol_packet_weight, $BBlistingPrice);
                    $price_sg =  $price_convert->USATOSG($vol_packet_weight, $BBlistingPrice);


                    $price_us_source = [
                        'usa_to_in_b2c' => $price_in_b2c,
                        'usa_to_in_b2b' => $price_in_b2b,
                        'usa_to_uae' => $price_ae,
                        'usa_to_sg' => $price_sg,
                        'weight' => $packet_weight,
                        'volumetric_weight_pounds' => $volumetricPounds,
                        'volumetric_weight_kg' => $volumetricKg
                    ];

                    $pricing_us[] = [...$asinDetails, ...$price_us_source];
                    if ($count1 == 1000) {
                        PricingUs::upsert($pricing_us, 'unique_asin',  [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'weight',
                            'volumetric_weight_pounds',
                            'volumetric_weight_kg',
                            'us_price',
                            'usa_to_in_b2b',
                            'usa_to_in_b2c',
                            'usa_to_uae',
                            'usa_to_sg',
                            'next_highest_seller_price',
                            'next_highest_seller_id',
                            'next_lowest_seller_price',
                            'next_lowest_seller_id',
                            'bb_winner_price',
                            'bb_winner_id',
                            'is_any_our_seller_won_bb',
                            'price_updated_at'
                        ]);
                        $count1 = 0;
                        $pricing_us = [];
                    }
                } elseif ($country_code_lr == 'in') {

                    $packet_weight_kg = poundToKg($packet_weight);
                    $vol_packet_weight_kg = $volumetricKg > $packet_weight_kg ? $volumetricKg : $packet_weight_kg;
                    $price_saudi = $price_convert->INDToSA($vol_packet_weight_kg, $BBlistingPrice);
                    $price_singapore = $price_convert->INDToSG($vol_packet_weight_kg, $BBlistingPrice);
                    $price_uae = $price_convert->INDToUAE($vol_packet_weight_kg, $BBlistingPrice);

                    $destination_price = [
                        'ind_to_uae' => $price_uae,
                        'ind_to_sg' => $price_singapore,
                        'ind_to_sa' => $price_saudi,
                        'weight' => $packet_weight_kg,
                        'volumetric_weight_pounds' => $volumetricPounds,
                        'volumetric_weight_kg' => $volumetricKg
                    ];
                    $pricing_in[] = [...$asinDetails, ...$destination_price];
                    if ($count1 == 1000) {
                        PricingIn::upsert($pricing_in, 'asin_unique', [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'in_price',
                            'weight',
                            'volumetric_weight_pounds',
                            'volumetric_weight_kg',
                            'ind_to_uae',
                            'ind_to_sg',
                            'ind_to_sa',
                            'next_highest_seller_price',
                            'next_highest_seller_id',
                            'next_lowest_seller_price',
                            'next_lowest_seller_id',
                            'bb_winner_price',
                            'bb_winner_id',
                            'is_any_our_seller_won_bb',
                            'price_updated_at'
                        ]);
                        $count1 = 0;
                        $pricing_in = [];
                    }
                }
                $count1++;
            }
            if ($country_code_lr == 'us') {

                PricingUs::upsert($pricing_us, 'unique_asin',  [
                    'asin',
                    'available',
                    'is_sold_by_amazon',
                    'weight',
                    'volumetric_weight_pounds',
                    'volumetric_weight_kg',
                    'us_price',
                    'usa_to_in_b2b',
                    'usa_to_in_b2c',
                    'usa_to_uae',
                    'usa_to_sg',
                    'next_highest_seller_price',
                    'next_highest_seller_id',
                    'next_lowest_seller_price',
                    'next_lowest_seller_id',
                    'bb_winner_price',
                    'bb_winner_id',
                    'is_any_our_seller_won_bb',
                    'price_updated_at'
                ]);
            } elseif ($country_code_lr == 'in') {

                PricingIn::upsert($pricing_in, 'asin_unique', [
                    'asin',
                    'available',
                    'is_sold_by_amazon',
                    'in_price',
                    'weight',
                    'volumetric_weight_pounds',
                    'volumetric_weight_kg',
                    'ind_to_uae',
                    'ind_to_sg',
                    'ind_to_sa',
                    'next_highest_seller_price',
                    'next_highest_seller_id',
                    'next_lowest_seller_price',
                    'next_lowest_seller_id',
                    'bb_winner_price',
                    'bb_winner_id',
                    'is_any_our_seller_won_bb',
                    'price_updated_at'
                ]);
            }
            // Log::alert($pricing_us);
        }
    }
}
