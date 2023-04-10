<?php

namespace App\Services\Catalog;

use Carbon\Carbon;
use App\Models\Catalog\PricingAe;
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

            // $BuyBoxRecords = DB::connection('buybox')
            //     ->select("SELECT PPO.asin, LP.available, 
            //                 LP.is_sold_by_amazon,
            //                 LP.is_any_our_seller_own_bb, 
            //                 LP.next_highest_seller_price,
            //                 LP.next_highest_seller_id,
            //                 LP.next_lowest_seller_price,
            //                 LP.next_lowest_seller_id,
            //                 LP.bb_winner_price,
            //                 LP.bb_winner_id,
            //                 LP.updated_at as updated_at,
            //                     GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
            //                     group_concat(PPO.listingprice_amount) as listingprice_amount
            //                     FROM $product_seller_details as PPO
            //                     JOIN $product_lp as LP 
            //                     ON PPO.asin = LP.asin
            //                     WHERE LP.updated_at BETWEEN $start AND $end 
            //                     GROUP BY PPO.asin
            //                 ");

            $BuyBoxRecords = DB::connection('buybox')
                ->select("SELECT asin, 
                            available, 
                            available_price
                            FROM $product_lp  
                            WHERE updated_at BETWEEN $start AND $end
                            ");
            $count = 0;
            $asins = [];
            $Records = [];
            $catalogRecords = [];

            // Log::notice($country_code_lr . '=>' . count($BuyBoxRecords));
            $catalogTable = table_model_create(country_code: $country_code_lr, model: 'Catalog', table_name: 'catalog');
            foreach ($BuyBoxRecords as $BuyBoxRecord) {

                $Records[$BuyBoxRecord->asin] = $BuyBoxRecord;
                $asins[] = $BuyBoxRecord->asin;

                if ($count == 1000) {
                    $catalogRecords[] = $catalogTable->select('asin', 'weight', 'height', 'length', 'width')
                        ->whereIn('asin', $asins)
                        ->get()
                        ->toArray();

                    $count = 0;
                    $asins = [];
                }
                $count++;
            }
            $catalogRecords[] = $catalogTable->select('asin', 'weight', 'height', 'length', 'width')
                ->whereIn('asin', $asins)
                ->get()
                ->toArray();


            $BBRecords = [];
            $catalogWeight = [];
            $asinDetails = [];

            foreach ($catalogRecords as $catalogRecord) {
                foreach ($catalogRecord as $catalog) {

                    $BBRecords[] = $Records[$catalog['asin']];

                    $catalogWeight[$catalog['asin']]['weight'] = $catalog['weight'] ?? '0.5';
                    $catalogWeight[$catalog['asin']]['height'] = $catalog['height'] ?? 0;
                    $catalogWeight[$catalog['asin']]['length'] = $catalog['length'] ?? 0;
                    $catalogWeight[$catalog['asin']]['width'] = $catalog['width'] ?? 0;
                }
            }
            // Log::notice($catalogWeight);
            // $buybox_landed_price = '';
            $pricing_in = [];
            $pricing_us = [];
            $pricing_ae = [];
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
                $available_price = $BBRecord->available_price;

                // $is_sold_by_amazon = $BBRecord->is_sold_by_amazon;
                // $is_our_seller_bb_winner = $BBRecord->is_any_our_seller_own_bb;
                // $next_highest_seller_price = $BBRecord->next_highest_seller_price;
                // $next_highest_seller_id = $BBRecord->next_highest_seller_id;
                // $next_lowest_seller_price = $BBRecord->next_lowest_seller_price;
                // $next_lowest_seller_id = $BBRecord->next_lowest_seller_id;
                // $bb_winner_price = $BBRecord->bb_winner_price;
                // $bb_winner_id = $BBRecord->bb_winner_id;
                // $updated_at = $BBRecord->updated_at;

                // $isBuyBoxWinner = explode(',', $BBRecord->is_buybox_winner);
                // $listingAmount = explode(',', $BBRecord->listingprice_amount);
                // $lowestprice_condition = $BBRecord->lowestprice_condition;
                // $buybox_condition = $BBRecord->buybox_condition;
                // $buybox_listingprice_amount = $BBRecord->buybox_listingprice_amount;
                // $buybox_landedprice_amount = $BBRecord->buybox_landedprice_amount;
                // $lowestprice_landedprice_amount = $BBRecord->lowestprice_landedprice_amount;
                // $lowestprice_listingprice_amount = $BBRecord->lowestprice_listingprice_amount;

                $volumetricPounds = VolumetricIntoPounds($dimension);
                $volumetricKg = VolumetricIntoKG($dimension);

                // foreach ($isBuyBoxWinner as $key1 => $BuyBoxWinner) {
                $price = $country_code_lr . '_price';
                // $buybox_price = 0;
                // if ($BuyBoxWinner == 1) {
                // if ($country_code_lr == 'us') {

                //     if ($bb_winner_price != '') {

                //         $buybox_price = $bb_winner_price;
                //     } else {

                //         if ($buybox_condition == 'new') {
                //             $buybox_price = $buybox_landedprice_amount != '' ? $buybox_landedprice_amount : ($buybox_listingprice_amount != '' ? $buybox_listingprice_amount : 0);
                //         }
                //     }
                // } elseif ($country_code_lr == 'in') {

                //     if ($buybox_condition == 'new') {

                //         $buybox_price = $buybox_landedprice_amount != '' ? $buybox_landedprice_amount : ($lowestprice_landedprice_amount != '' ? $lowestprice_landedprice_amount : ($lowestprice_listingprice_amount != '' ? $lowestprice_listingprice_amount : 0));
                //     } elseif ($buybox_condition != 'new' && $lowestprice_condition == 'new') {

                //         $buybox_price = $buybox_landedprice_amount != '' ? $buybox_landedprice_amount : ($lowestprice_landedprice_amount != '' ? $lowestprice_landedprice_amount : ($lowestprice_listingprice_amount != '' ? $lowestprice_listingprice_amount : 0));
                //     }
                // } elseif ($country_code_lr == 'ae') {

                //     if ($buybox_condition == 'new') {

                //         $buybox_price = $buybox_landedprice_amount != '' ? $buybox_landedprice_amount : ($lowestprice_landedprice_amount != '' ? $lowestprice_landedprice_amount : ($lowestprice_listingprice_amount != '' ? $lowestprice_listingprice_amount : 0));
                //     } elseif ($buybox_condition != 'new' && $lowestprice_condition == 'new') {

                //         $buybox_price = $buybox_landedprice_amount != '' ? $buybox_landedprice_amount : ($lowestprice_landedprice_amount != '' ? $lowestprice_landedprice_amount : ($lowestprice_listingprice_amount != '' ? $lowestprice_listingprice_amount : 0));
                //     }
                // }
                // if ($buybox_price != 0) {

                $asinDetails = [
                    'asin'                      => $asin,
                    'available'                 => $available_price != 0 ? $available : 0,
                    $price                      => $available_price,
                    // 'is_sold_by_amazon'         => $is_sold_by_amazon,
                    // 'next_highest_seller_price' => $next_highest_seller_price,
                    // 'next_highest_seller_id'    => $next_highest_seller_id,
                    // 'next_lowest_seller_price'  => $next_lowest_seller_price,
                    // 'next_lowest_seller_id'     => $next_lowest_seller_id,
                    // 'bb_winner_price'           => $bb_winner_price,
                    // 'bb_winner_id'              => $bb_winner_id,
                    // 'is_any_our_seller_won_bb'  => $is_our_seller_bb_winner,
                    // 'price_updated_at'          => $updated_at,
                ];

                // break 1;
                // } else {
                //     $BBlistingPrice = min($listingAmount);

                //     $asinDetails = [
                //         'asin'                      => $asin,
                //         'available'                 => $available,
                //         'is_sold_by_amazon'         => $is_sold_by_amazon,
                //         $price                      => $BBlistingPrice,
                //         'next_highest_seller_price' => $next_highest_seller_price,
                //         'next_highest_seller_id'    => $next_highest_seller_id,
                //         'next_lowest_seller_price'  => $next_lowest_seller_price,
                //         'next_lowest_seller_id'     => $next_lowest_seller_id,
                //         'bb_winner_price'           => $bb_winner_price,
                //         'bb_winner_id'              => $bb_winner_id,
                //         'is_any_our_seller_won_bb'  => $is_our_seller_bb_winner,
                //         'price_updated_at'          => $updated_at,
                //     ];
                // }
                // }
                if ($country_code_lr == 'us') {
                    if ($available_price != 0) {

                        $vol_packet_weight = $volumetricPounds > $packet_weight ? $volumetricPounds : $packet_weight;
                        $price_in_b2c = $price_convert->USAToINDB2C($vol_packet_weight, $available_price);
                        $price_in_b2b = $price_convert->USAToINDB2B($vol_packet_weight, $available_price);
                        $price_ae = $price_convert->USATOUAE($vol_packet_weight, $available_price);
                        $price_sg =  $price_convert->USATOSG($vol_packet_weight, $available_price);

                        $price_us_source = [
                            'usa_to_in_b2c' => $price_in_b2c ?? 0,
                            'usa_to_in_b2b' => $price_in_b2b ?? 0,
                            'usa_to_uae' => $price_ae ?? 0,
                            'usa_to_sg' => $price_sg ?? 0,
                            'weight' => $packet_weight ?? 0,

                        ];
                    } else {
                        $price_us_source = [
                            'usa_to_in_b2c' =>  0,
                            'usa_to_in_b2b' =>  0,
                            'usa_to_uae' =>  0,
                            'usa_to_sg' =>  0,
                            'weight' =>  0,

                        ];
                    }

                    $pricing_us[] = [...$asinDetails, ...$price_us_source];
                    if ($count1 == 1000) {
                        // Log::warning($country_code_lr . '=>' . count($pricing_us));
                        PricingUs::upsert($pricing_us, ['unique_asin'],  [
                            'asin',
                            'available',
                            'weight',
                            'us_price',
                            'usa_to_in_b2b',
                            'usa_to_in_b2c',
                            'usa_to_uae',
                            'usa_to_sg',

                        ]);
                        $count1 = 0;
                        $pricing_us = [];
                    }
                } elseif ($country_code_lr == 'in') {
                    if ($available_price != 0) {

                        $packet_weight_kg = poundToKg($packet_weight);
                        $vol_packet_weight_kg = $volumetricKg > $packet_weight_kg ? $volumetricKg : $packet_weight_kg;
                        $price_saudi = $price_convert->INDToSA($vol_packet_weight_kg, $available_price);
                        $price_singapore = $price_convert->INDToSG($vol_packet_weight_kg, $available_price);
                        $price_uae = $price_convert->INDToUAE($vol_packet_weight_kg, $available_price);

                        $destination_price = [
                            'ind_to_uae' => $price_uae ?? 0,
                            'ind_to_sg' => $price_singapore ?? 0,
                            'ind_to_sa' => $price_saudi ?? 0,
                            'weight' => $packet_weight_kg ?? 0,
                        ];
                    } else {
                        $destination_price = [
                            'ind_to_uae' =>  0,
                            'ind_to_sg' =>  0,
                            'ind_to_sa' =>  0,
                            'weight' =>  0,
                        ];
                    }

                    $pricing_in[] = [...$asinDetails, ...$destination_price];
                    if ($count1 == 1000) {
                        PricingIn::upsert($pricing_in, ['asin_unique'], [
                            'asin',
                            'available',
                            'in_price',
                            'weight',
                            'ind_to_uae',
                            'ind_to_sg',
                            'ind_to_sa',

                        ]);
                        $count1 = 0;
                        $pricing_in = [];
                    }
                } else if ($country_code_lr == 'ae') {

                    $destination_weight = ['weight' => $packet_weight];
                    $pricing_ae[] = [...$asinDetails, ...$destination_weight];
                    if ($count1 == 1000) {

                        PricingAe::upsert($pricing_ae, ['unique_asin'],  [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'weight',
                            'volumetric_weight_pounds',
                            'volumetric_weight_kg',
                            'ae_price',
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
                        $pricing_ae = [];
                    }
                }
                $count1++;
                // }
            }
            if ($country_code_lr == 'us') {
                // Log::warning($country_code_lr . '=>' . count($pricing_us));
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
            } else if ($country_code_lr == 'ae') {

                PricingAe::upsert($pricing_ae, ['unique_asin'],  [
                    'asin',
                    'available',
                    'is_sold_by_amazon',
                    'weight',
                    'volumetric_weight_pounds',
                    'volumetric_weight_kg',
                    'ae_price',
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
