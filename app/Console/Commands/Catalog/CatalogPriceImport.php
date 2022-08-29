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
            'IN' => 39
            // 'US' => 40
        ];
        $price_convert = new PriceConversion();
        $chunk = 10;
        foreach ($source as $country_code => $seller_id) {

            $country_code_lr = strtolower($country_code);

            $product_seller_details = 'bb_product_' . $country_code_lr . 's_seller_details';
            $product_lp = 'bb_product_' . $country_code_lr . 's_lp_offers';

            // $catalog_table = 'catalog' . $country_code_lr . 's';
            // AsinDestination::select('asin_destinations.asin', "$catalog_table.package_dimensions")
            //     ->where('asin_destinations.destination', $country_code)
            //     ->join($catalog_table, 'asin_destinations.asin', '=', "$catalog_table.asin")

            $table_name = 'catalog' . $country_code_lr . 's';
            $modal_table = table_model_create(country_code: $this->country_code, model: 'Catalog', table_name: 'catalognew');

            $modal_table->select(['asin', 'dimensioins'])->chunk($chunk, function ($data) use ($seller_id, $country_code_lr, $product_seller_details, $product_lp, $price_convert) {

                $pricing = [];
                $pricing_in = [];
                $asin_details = [];
                $listing_price_amount = '';

                $asin_array = [];
                foreach ($data as $value) {
                    $weight = '0.5';

                    if (isset(json_decode($value->dimensions)[0]->package->weight->value)) {
                        $weight = json_decode($value->dimensions)[0]->package->weight->value;
                    }

                    $a = $value->asin;
                    $calculated_weight[$a] =  $weight;
                    $asin_array[] = "'$a'";
                }

                $asin = implode(',', $asin_array);

                $asin_price = DB::connection('buybox')
                    ->select("SELECT PPO.asin, LP.available,
                    GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                    group_concat(PPO.listingprice_amount) as listingprice_amount,
                    group_concat(PPO.updated_at) as updated_at
                    FROM $product_seller_details as PPO
                    JOIN $product_lp as LP
                        WHERE PPO.asin = LP.asin
                        AND PPO.asin IN ($asin)
                        GROUP BY PPO.asin
                    ");

                foreach ($asin_price as $value) {

                    $buybox_winner = explode(',', $value->is_buybox_winner);
                    $listing_price = explode(',', $value->listingprice_amount);
                    $updated_at = explode(',', $value->updated_at);

                    $asin_name = $value->asin;
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
                if ($country_code_lr == 'us') {

                    PricingUs::upsert($pricing, 'unique_asin',  ['asin', 'available', 'weight', 'us_price', 'usa_to_in_b2b', 'usa_to_in_b2c', 'usa_to_uae', 'usa_to_sg', 'price_updated_at']);
                } elseif ($country_code_lr == 'in') {

                    PricingIn::upsert($pricing_in, 'asin_unique', ['asin', 'available', 'in_price', 'weight', 'ind_to_uae', 'ind_to_sg', 'ind_to_sa', 'price_updated_at']);
                }
                // exit;
            });
        }
    }
}
