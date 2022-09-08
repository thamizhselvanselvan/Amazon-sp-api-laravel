<?php

namespace App\Services\Catalog;

use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuyBoxPriceImport
{
    public function fetchPriceFromBB($country_code, $seller_id, $limit)
    {
        $i = 0;
        for ($i; $i < 7; $i++) {

            $product_lp = '';

            $start = startTime();

            $price_convert = new PriceConversion();

            $user_id = '';
            $des_asin_array = [];
            $calculated_weight = [];

            $des_asin_update = [];
            $find_missing_asin = [];

            $country_code_lr = strtolower($country_code);

            $product_seller_details = 'bb_product_' . $country_code_lr . 's_seller_details';
            $product_lp = 'bb_product_' . $country_code_lr . 's_lp_offers';

            $destination_model = table_model_create(country_code: $country_code, model: 'Asin_destination', table_name: 'asin_destination_');

            $data = $destination_model->select(['asin', 'user_id'])->where(['price_status' => '0'])->limit($limit)->get();

            foreach ($data as $value) {

                $asin = $value->asin;
                $des_asin_array[] =  $asin;
                $user_id = $value->user_id;

                $find_missing_asin[$asin] = 1;
                $des_asin_update[] = [
                    'asin' => $asin,
                    'user_id' => $user_id,
                    'price_status' => '2'
                ];
            }

            $destination_model->upsert($des_asin_update, 'user_asin_unique', ['price_status']);
            $des_asin_update = [];

            $catalog_model = table_model_create(country_code: $country_code_lr, model: 'Catalog', table_name: 'catalognew');
            $cat_data = $catalog_model->select(['asin', 'dimensions'])->whereIn('asin', $des_asin_array)->get();

            $pricing = [];
            $pricing_in = [];
            $asin_details = [];
            $listing_price_amount = '';

            $asin_array = [];
            foreach ($cat_data as $value) {
                $weight = '0.5';

                if (isset(json_decode($value->dimensions)[0]->package->weight->value)) {
                    $weight = json_decode($value->dimensions)[0]->package->weight->value;
                }

                $a = $value->asin;
                $calculated_weight[$a] =  $weight;
                $asin_array[] = "'$a'";
            }

            if ($asin_array) {

                $asin = implode(',', $asin_array);

                Log::notice("Before BB Select Query - " . endTime($start));

                $asin_price = DB::connection('buybox')
                    ->select("SELECT PPO.asin, LP.available,
                GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                group_concat(PPO.listingprice_amount) as listingprice_amount,
                group_concat(PPO.updated_at) as updated_at
                FROM $product_seller_details as PPO
                JOIN $product_lp as LP On PPO.asin = LP.asin
                    WHERE PPO.asin IN ($asin)
                    GROUP BY PPO.asin
                ");


                Log::notice("After BB Select Query - " . endTime($start));

                foreach ($asin_price as $value) {

                    $buybox_winner = explode(',', $value->is_buybox_winner);
                    $listing_price = explode(',', $value->listingprice_amount);
                    $updated_at = explode(',', $value->updated_at);

                    $asin_name = $value->asin;

                    if (isset($find_missing_asin[$asin_name])) {

                        $des_asin_update[] = [
                            'asin' => $asin_name,
                            'user_id' => $user_id,
                            'price_status' => '1'
                        ];
                    }

                    $available = $value->available;
                    $packet_weight = $calculated_weight[$asin_name];

                    foreach ($buybox_winner as $key =>  $value1) {

                        $price = $country_code_lr . '_price';
                        if ($value1 == '1') {

                            $listing_price_amount = $listing_price[$key];

                            $asin_details =
                                [
                                    'asin' =>  $asin_name,
                                    'available' => $available,
                                    $price => $listing_price_amount,
                                    'price_updated_at' => max($updated_at),
                                ];
                            break 1;
                        } else {

                            $listing_price_amount =  min($listing_price);
                            $asin_details =
                                [
                                    'asin' =>  $asin_name,
                                    'available' => $available,
                                    $price => $listing_price_amount,
                                    'price_updated_at' =>  max($updated_at),
                                ];
                        }
                    }
                    if ($country_code_lr == 'us') {

                        $price_in_b2c = $price_convert->USAToINDB2C($packet_weight, $listing_price_amount);

                        $price_in_b2b = $price_convert->USAToINDB2B($packet_weight, $listing_price_amount);

                        $price_ae = $price_convert->USATOUAE($packet_weight, $listing_price_amount);

                        $price_sg =  $price_convert->USATOSG($packet_weight, $listing_price_amount);

                        $price_us_source = [
                            'usa_to_in_b2c' => $price_in_b2c,
                            'usa_to_in_b2b' => $price_in_b2b,
                            'usa_to_uae' => $price_ae,
                            'usa_to_sg' => $price_sg,
                            'weight' => $packet_weight
                        ];

                        $pricing[] = [...$asin_details, ...$price_us_source];
                    } elseif ($country_code_lr == 'in') {

                        $packet_weight_kg = poundToKg($packet_weight);

                        $price_saudi = $price_convert->INDToSA($packet_weight_kg, $listing_price_amount);
                        $price_singapore = $price_convert->INDToSG($packet_weight_kg, $listing_price_amount);
                        $price_uae = $price_convert->INDToUAE($packet_weight_kg, $listing_price_amount);

                        $destination_price = [
                            'ind_to_uae' => $price_uae,
                            'ind_to_sg' => $price_singapore,
                            'ind_to_sa' => $price_saudi,
                            'weight' => $packet_weight_kg
                        ];
                        $pricing_in[] = [...$asin_details, ...$destination_price];
                    }
                }

                $destination_model->upsert($des_asin_update, 'user_asin_unique', ['price_status']);

                if ($country_code_lr == 'us') {

                    PricingUs::upsert($pricing, 'unique_asin',  ['asin', 'available', 'weight', 'us_price', 'usa_to_in_b2b', 'usa_to_in_b2c', 'usa_to_uae', 'usa_to_sg', 'price_updated_at']);
                } elseif ($country_code_lr == 'in') {

                    PricingIn::upsert($pricing_in, 'asin_unique', ['asin', 'available', 'in_price', 'weight', 'ind_to_uae', 'ind_to_sg', 'ind_to_sa', 'price_updated_at']);
                }
            } else {

                //if all price are fetched then update status
                $destination_model->where('id', '>', '0')->update(['price_status' => '0']);
                break;
            }
        }
        Log::notice("Entire Process Finish - " . endTime($start));
    }
}
