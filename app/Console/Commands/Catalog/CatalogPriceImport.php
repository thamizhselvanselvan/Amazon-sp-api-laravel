<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use Illuminate\Support\Facades\Log;
use App\Models\Catalog\AsinDestination;
use App\Services\Catalog\PriceConversion;

class CatalogPriceImport extends Command
{
    public $rate_master_in_ae;
    public $rate_master_in_sa;
    public $rate_master_in_sg;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Catalog-price-import-bb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import catalog from buy box';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $product = '';
        $product_lp = '';
        $seller_id = '';

        // $source = buyboxCountrycode();
        $source = [
            // 'IN' => 39,
            'US' => 40
        ];
        $price_convert = new PriceConversion();
        $chunk = 10;
        foreach ($source as $country_code => $seller_id) {

            $country_code_lr = strtolower($country_code);

            $product_lp = 'bb_product_' . $country_code_lr . 's_seller_details';
            $product = 'bb_product_' . $country_code_lr . 's';

            $catalog_table = 'catalog' . $country_code_lr . 's';
            AsinDestination::select('asin_destinations.asin', "$catalog_table.package_dimensions")
                ->where('asin_destinations.destination', $country_code)
                ->join($catalog_table, 'asin_destinations.asin', '=', "$catalog_table.asin")
                ->chunk($chunk, function ($data) use ($seller_id, $country_code_lr, $product_lp, $price_convert) {

                    $pricing = [];
                    $pricing_in = [];
                    $asin_details = [];
                    $listing_price_amount = '';

                    $asin_array = [];
                    foreach ($data as $value) {

                        $dimension = ($value->package_dimensions);

                        $a = $value->asin;
                        $weight_value = 0.5;
                        // if (isset($weight->value)) {

                        //     $weight_value = $weight->value;
                        // }
                        $calculated_weight[$a] = getWeight($dimension);
                        $asin_array[] = "'$a'";
                        Log::info($calculated_weight[$a]);
                    }
                    exit;
                    $asin = implode(',', $asin_array);
                    $asin_price = DB::connection('buybox')
                        ->select("SELECT PPO.asin,
                    GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                    group_concat(PPO.listingprice_amount) as listingprice_amount,
                    group_concat(PPO.updated_at) as updated_at
                    FROM $product_lp as PPO
                        WHERE PPO.asin IN ($asin)
                        GROUP BY PPO.asin
                    ");

                    foreach ($asin_price as $value) {

                        $buybox_winner = explode(',', $value->is_buybox_winner);
                        $listing_price = explode(',', $value->listingprice_amount);
                        $updated_at = explode(',', $value->updated_at);

                        $asin_name = $value->asin;
                        $packet_weight = $calculated_weight[$asin_name];

                        foreach ($buybox_winner as $key =>  $value1) {

                            $price = $country_code_lr . '_price';
                            if ($value1 == '1') {

                                $listing_price_amount = $listing_price[$key];
                                $asin_details =
                                    [
                                        'asin' =>  $asin_name,
                                        'weight' => $packet_weight,
                                        $price => $listing_price_amount,
                                        'price_updated_at' => $updated_at[$key] ? $updated_at[$key] : NULL,
                                    ];
                                break 1;
                            } else {
                                $listing_price_amount =  min($listing_price);
                                $asin_details =
                                    [
                                        'asin' =>  $asin_name,
                                        'weight' => $packet_weight,
                                        $price => $listing_price_amount,
                                        'price_updated_at' => $updated_at[$key] ? $updated_at[$key] : NULL,
                                    ];
                            }
                        }
                        if ($country_code_lr == 'us') {

                            $ind_price = $price_convert->USAToIND($packet_weight, $listing_price_amount);
                            $destination_price_in = [
                                'ind_sp' => $ind_price,
                            ];

                            $destination_price_ae = [
                                'uae_sp' => $price_convert->USATOUAE($packet_weight, $listing_price_amount)
                            ];

                            $destination_price_sg = [
                                'sg_sp' => $price_convert->USATOSG($packet_weight, $listing_price_amount),
                            ];

                            $pricing[] = [...$asin_details, ...$destination_price_in, ...$destination_price_ae, ...$destination_price_sg];
                        } elseif ($country_code_lr == 'in') {

                            $price_saudi = $price_convert->INDToSA($packet_weight, $listing_price_amount);
                            $price_singapore = $price_convert->INDToSG($packet_weight, $listing_price_amount);
                            $price_uae = $price_convert->INDToUAE($packet_weight, $listing_price_amount);

                            $destination_price = [
                                'uae_sp' => $price_uae,
                                'sg_sp' => $price_singapore,
                                'sa_sp' => $price_saudi,
                            ];
                            $pricing_in[] = [...$asin_details, ...$destination_price];
                        }
                    }
                    if ($country_code_lr == 'us') {

                        PricingUs::upsert($pricing, 'unique_asin', ['asin', 'weight', 'us_price', 'ind_sp', 'uae_sp', 'sg_sp', 'price_updated_at']);
                    } elseif ($country_code_lr == 'in') {

                        PricingIn::upsert($pricing_in, 'asin_unique', ['asin', 'weight', 'in_price', 'uae_sp', 'sg_sp', 'sa_sp', 'price_updated_at']);
                    }
                    // exit;
                });
        }
    }
}
