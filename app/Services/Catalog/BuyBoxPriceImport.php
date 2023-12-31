<?php

namespace App\Services\Catalog;

use App\Models\Mws_region;
use App\Models\Catalog\PricingAe;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingSa;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use App\Services\Catalog\PriceConversion;

class BuyBoxPriceImport
{
    public function fetchPriceFromBB($country_code, $seller_id, $limit)
    {
        $priority_array = ['P1' => 1, 'P2' => 2, 'P3' => 3, 'P4' => 4];
        $price_convert = new PriceConversion();

        foreach ($priority_array as $priority) {

            $product_lp = '';
            $country_code_lr = strtolower($country_code);

            $user_id = '';
            $des_asin_array = [];
            $calculated_weight = [];

            $des_asin_update = [];
            $find_missing_asin = [];

            $product_seller_details = "bb_product_aa_custom_p${priority}_${country_code_lr}_seller_details";
            $product_lp = "bb_product_aa_custom_p${priority}_${country_code_lr}_offers";

            $destination_model = table_model_create(country_code: $country_code, model: 'Asin_destination', table_name: 'asin_destination_');

            $data = $destination_model->select(['asin', 'user_id'])
                ->where('price_status', 0)->where('priority', $priority)
                ->limit($limit)->get();


            if (count($data) > 0) {


                foreach ($data as $value) {

                    $asin = $value->asin;
                    $des_asin_array[] =  $asin;
                    $user_id = $value->user_id;

                    $find_missing_asin[$asin] = 1;
                    $des_asin_update[] = [
                        'asin' => $asin,
                        'user_id' => $user_id,
                        'price_status' => 2
                    ];
                }

                $destination_model->where('priority', $priority)->upsert($des_asin_update, 'user_asin_unique', ['price_status']);
                $des_asin_update = [];

                $catalog_model = table_model_create(country_code: $country_code_lr, model: 'Catalog', table_name: 'catalognew');
                $cat_data = $catalog_model->select(['asin', 'dimensions'])->whereIn('asin', $des_asin_array)->get();

                $pricing = [];
                $pricing_in = [];
                $pricing_ae_sa = [];
                $asin_details = [];
                $listing_price_amount = '';
                $unavaliable_asin = [];

                $asin_array = [];

                foreach ($cat_data as $value) {
                    $weight = '0.5';

                    if (isset(json_decode($value->dimensions)[0]->package->weight->value)) {
                        $weight = json_decode($value->dimensions)[0]->package->weight->value;
                    }

                    $a = $value->asin;
                    $calculated_weight[$a] =  $weight;
                    $asin_array[] = "'$a'";

                    $unavaliable_asin[] = [
                        'asin'                      => $a,
                        'available'                 => 0,
                        'is_sold_by_amazon'         => 0,
                        'next_highest_seller_price' => 0,
                        'next_highest_seller_id'    => 0,
                        'next_lowest_seller_price'  => 0,
                        'next_lowest_seller_id'     => 0,
                        'bb_winner_price'           => 0,
                        'bb_winner_id'              => 0,
                        'is_any_our_seller_won_bb'  => 0,
                    ];
                }

                if ($asin_array) {

                    $asin = implode(',', $asin_array);
                    $asin_price = DB::connection('buybox')
                        ->select("SELECT PPO.asin, LP.available, LP.is_sold_by_amazon,LP.is_any_our_seller_own_bb, 
                        LP.next_highest_seller_price,
                        LP.next_highest_seller_id,
                        LP.next_lowest_seller_price,
                        LP.next_lowest_seller_id,
                        LP.bb_winner_price,
                        LP.bb_winner_id,
                        LP.updated_at as updated_at,
                            GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                            group_concat(PPO.listingprice_amount) as listingprice_amount
                            FROM 
                                $product_seller_details as PPO
                                    JOIN
                                 $product_lp as LP 
                            ON 
                                PPO.asin = LP.asin
                            WHERE
                                 PPO.asin IN ($asin)
                            GROUP BY 
                                PPO.asin
                        ");

                    foreach ($asin_price as $value) {

                        $buybox_winner = explode(',', $value->is_buybox_winner);
                        $listing_price = explode(',', $value->listingprice_amount);
                        // $updated_at = explode(',', $value->updated_at);
                        $updated_at = $value->updated_at;

                        $asin_name = $value->asin;
                        if (isset($find_missing_asin[$asin_name])) {

                            $des_asin_update[] = [
                                'asin' => $asin_name,
                                'user_id' => $user_id,
                                'price_status' => 1
                            ];
                        }

                        $available                  = $value->available;
                        $packet_weight              = $calculated_weight[$asin_name];
                        $is_sold_by_amazon          = $value->is_sold_by_amazon;
                        $is_any_our_seller_won_bb   = $value->is_any_our_seller_own_bb;
                        $next_highest_seller_price  = $value->next_highest_seller_price;
                        $next_highest_seller_id     = $value->next_highest_seller_id;
                        $next_lowest_seller_price   = $value->next_lowest_seller_price;
                        $next_lowest_seller_id      = $value->next_lowest_seller_id;
                        $bb_winner_price            = $value->bb_winner_price;
                        $bb_winner_id               = $value->bb_winner_id;

                        foreach ($buybox_winner as $key =>  $value1) {

                            $price = $country_code_lr . '_price';
                            if ($value1 == '1') {

                                $listing_price_amount = $listing_price[$key];

                                $asin_details =
                                    [
                                        'asin'                      =>  $asin_name,
                                        'available'                 => $available,
                                        'is_sold_by_amazon'         => $is_sold_by_amazon,
                                        $price                      => $listing_price_amount,
                                        // 'price_updated_at' => $updated_at[array_key_last($updated_at)],
                                        'next_highest_seller_price' => $next_highest_seller_price,
                                        'next_highest_seller_id'    => $next_highest_seller_id,
                                        'next_lowest_seller_price'  => $next_lowest_seller_price,
                                        'next_lowest_seller_id'     => $next_lowest_seller_id,
                                        'bb_winner_price'           => $bb_winner_price,
                                        'bb_winner_id'              => $bb_winner_id,
                                        'is_any_our_seller_won_bb'  => $is_any_our_seller_won_bb,
                                        'price_updated_at'          => $updated_at,
                                    ];
                                break 1;
                            } else {

                                $listing_price_amount =  min($listing_price);
                                $asin_details =
                                    [
                                        'asin'                      =>  $asin_name,
                                        'available'                 => $available,
                                        'is_sold_by_amazon'         => $is_sold_by_amazon,
                                        $price                      => $listing_price_amount,
                                        // 'price_updated_at' =>  max($updated_at),
                                        'next_highest_seller_price' => $next_highest_seller_price,
                                        'next_highest_seller_id'    => $next_highest_seller_id,
                                        'next_lowest_seller_price'  => $next_lowest_seller_price,
                                        'next_lowest_seller_id'     => $next_lowest_seller_id,
                                        'bb_winner_price'           => $bb_winner_price,
                                        'bb_winner_id'              => $bb_winner_id,
                                        'is_any_our_seller_won_bb'  => $is_any_our_seller_won_bb,
                                        'price_updated_at'          => $updated_at,
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
                        // elseif ($country_code_lr == 'ae' || $country_code_lr == 'sa') {

                        //     $destination_price = ['weight' => $packet_weight];
                        //     $pricing_ae_sa[] = [...$asin_details, ...$destination_price];
                        // }
                    }

                    // $destination_model->upsert($des_asin_update, 'user_asin_unique', ['price_status']);

                    if ($country_code_lr == 'us') {

                        PricingUs::upsert($unavaliable_asin, 'unique_asin', [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'next_highest_seller_price',
                            'next_highest_seller_id',
                            'next_lowest_seller_price',
                            'next_lowest_seller_id',
                            'bb_winner_price',
                            'bb_winner_id',
                            'is_any_our_seller_won_bb'
                        ]);
                        PricingUs::upsert($pricing, 'unique_asin',  [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'weight',
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

                        PricingIn::upsert($unavaliable_asin, 'unique_asin', [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'next_highest_seller_price',
                            'next_highest_seller_id',
                            'next_lowest_seller_price',
                            'next_lowest_seller_id',
                            'bb_winner_price',
                            'bb_winner_id',
                            'is_any_our_seller_won_bb'
                        ]);
                        PricingIn::upsert($pricing_in, 'asin_unique', [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'in_price',
                            'weight',
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
                        // $this->updateRecordIntoStoreTable($country_code, $asin_price);
                    } elseif ($country_code_lr == 'ae') {

                        PricingAe::upsert($unavaliable_asin, 'unique_asin', [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'next_highest_seller_price',
                            'next_highest_seller_id',
                            'next_lowest_seller_price',
                            'next_lowest_seller_id',
                            'bb_winner_price',
                            'bb_winner_id',
                            'is_any_our_seller_won_bb'
                        ]);
                        PricingAe::upsert($pricing_ae_sa, 'unique_asin', [
                            'asin',
                            'available',
                            'is_sold_by_amazon',
                            'weight',
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
                        // $this->updateRecordIntoStoreTable($country_code, $asin_price);
                    }
                    // elseif ($country_code_lr == 'sa') {
                    //     PricingSa::upsert($unavaliable_asin, 'unique_asin', ['asin', 'available']);
                    //     PricingSa::upsert($pricing_ae_sa, 'unique_asin', ['asin', 'available', 'weight', 'sa_price', 'price_updated_at']);
                    // }
                    // $this->updateRecordIntoStoreTable($country_code, $asin_price);
                }
            } else {
                //if all price are fetched fro selected priority then update status
                $prir_count = $destination_model->where('priority', $priority)->update(['price_status' => 0]);
                // Log::info("$priority -> $prir_count updated");
            }
        }
    }

    public function updateRecordIntoStoreTable($country_code, $records)
    {
        $lowest_key = [];
        $highest_key = [];
        $bb_winner_price = '';
        $seller_lowest_price = [];
        $seller_highest_price = [];
        $store_id = Mws_region::with('aws_verified1')->where('region_code', $country_code)->get()->toArray();
        foreach ($store_id[0]['aws_verified1'] as $merchant_id) {

            foreach ($records as $bb_asin) {

                $bb_store_key = [
                    'bb_cyclic' => '1',
                    'is_bb_won' => '0',
                    'bb_winner_id' => '',
                    'bb_winner_price' => '',
                    'lowest_seller_id' => '',
                    'lowest_seller_price' => '',
                    'highest_seller_id' => '',
                    'highest_seller_price' => ''
                ];

                $is_bb_winners = explode(',', $bb_asin->is_buybox_winner);
                $listingPrice_amount = explode(',', $bb_asin->listingprice_amount);
                $seller_store_id = explode(',', $bb_asin->seller_store_id);

                $bb_winner_price = '';
                foreach ($is_bb_winners as $key2 => $bb_won) {
                    if ($bb_won == 1) {

                        $bb_winner_price = $listingPrice_amount[$key2];

                        $bb_store_key['is_bb_won'] = $seller_store_id[$key2] == $merchant_id['merchant_id'] ? 1 : 0;
                        $bb_store_key['bb_winner_id'] = $seller_store_id[$key2];
                        $bb_store_key['bb_winner_price'] = $bb_winner_price;
                    }
                }
                foreach ($is_bb_winners as $key1 => $is_bb_winner) {

                    if ($bb_winner_price != '') {

                        if ($listingPrice_amount[$key1] > $bb_winner_price) {
                            $seller_highest_price[] = $listingPrice_amount[$key1];
                            $highest_key[] = $key1;
                        }

                        if ($listingPrice_amount[$key1] < $bb_winner_price) {
                            $seller_lowest_price[] = $listingPrice_amount[$key1];
                            $lowest_key[] = $key1;
                        }
                    }
                }
                if ($seller_highest_price != null) {

                    $bb_store_key['highest_seller_id'] = $seller_store_id[min($highest_key)];
                    $bb_store_key['highest_seller_price'] = min($seller_highest_price);
                    $highest_key = [];
                    $seller_highest_price = [];
                }
                if ($seller_lowest_price != null) {

                    $bb_store_key['lowest_seller_id'] = $seller_store_id[max($lowest_key)];
                    $bb_store_key['lowest_seller_price'] = max($seller_lowest_price);
                    $lowest_key = [];
                    $seller_lowest_price = [];
                }

                Product::where('store_id', $merchant_id['seller_id'])
                    ->where('asin', $bb_asin->asin)
                    ->update($bb_store_key);
                $bb_store_key = [];
            }
        }
    }
}
