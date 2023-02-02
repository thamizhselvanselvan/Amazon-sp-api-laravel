<?php

use Carbon\Carbon;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\Catalog\PriceConversion;
use App\Models\Buybox_stores\Product_Push;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Modal;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// use ConfigTrait;

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');
Auth::routes();
Route::get('login', 'Admin\HomeController@dashboard')->name('login');
Route::get('home', 'Admin\HomeController@dashboard')->name('home');

// include_route_files(__DIR__ . '/pms/');
Route::get('testing', function () {

    $start = "'" . Carbon::now()->subDay(2)->toDateTimeString() . "'";
    $end = "'" . Carbon::now()->toDateTimeString() . "'";

    $country_code_lr = 'us';
    $price_convert = new PriceConversion();
    $product_seller_details = "bb_product_aa_custom_p2_us_seller_details";
    $product_lp = "bb_product_aa_custom_p2_us_offers";

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
    $asins = [];
    $count = 0;
    $catalogRecords = [];
    $Records = [];
    po(count($BuyBoxRecords));
    $catalogTable = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');
    foreach ($BuyBoxRecords as $BuyBoxRecord) {

        $Records[$BuyBoxRecord->asin] = $BuyBoxRecord;
        $asins[] = $BuyBoxRecord->asin;

        if ($count == 500) {
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

    foreach ($catalogRecords as $catalogRecord) {
        foreach ($catalogRecord as $catalog) {
            $weight = '0.5';
            $BBRecords[] = $Records[$catalog['asin']];

            if (isset(json_decode($catalog['dimensions'])[0]->package->weight->value)) {
                $weight = json_decode($catalog['dimensions'])[0]->package->weight->value;
            }
            $catalogWeight[$catalog['asin']] = $weight;
        }
    }

    $BBlistingPrice = '';
    $pricing_in = [];
    $pricing_us = [];
    po(count($BBRecords));
    $count1 = 0;
    foreach ($BBRecords as $BBRecord) {

        $asin = $BBRecord->asin;
        $packet_weight = $catalogWeight[$asin];
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

        foreach ($isBuyBoxWinner as $key1 => $BuyBoxWinner) {
            $price = 'us_price';
            if ($BuyBoxWinner == 1) {

                $BBlistingPrice = $listingAmount[$key1];

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

            $price_in_b2c = $price_convert->USAToINDB2C($packet_weight, $BBlistingPrice);
            $price_in_b2b = $price_convert->USAToINDB2B($packet_weight, $BBlistingPrice);
            $price_ae = $price_convert->USATOUAE($packet_weight, $BBlistingPrice);
            $price_sg =  $price_convert->USATOSG($packet_weight, $BBlistingPrice);

            $price_us_source = [
                'usa_to_in_b2c' => $price_in_b2c,
                'usa_to_in_b2b' => $price_in_b2b,
                'usa_to_uae' => $price_ae,
                'usa_to_sg' => $price_sg,
                'weight' => $packet_weight
            ];

            $pricing_us[] = [...$asinDetails, ...$price_us_source];
            if ($count1 == 10) {
                PricingUs::upsert($pricing_us, 'unique_asin',  [
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
                $count1 = 0;
                $pricing_us = [];
            }
            // po($pricing_us);

        } elseif ($country_code_lr == 'in') {

            $packet_weight_kg = poundToKg($packet_weight);
            $price_saudi = $price_convert->INDToSA($packet_weight_kg, $BBlistingPrice);
            $price_singapore = $price_convert->INDToSG($packet_weight_kg, $BBlistingPrice);
            $price_uae = $price_convert->INDToUAE($packet_weight_kg, $BBlistingPrice);

            $destination_price = [
                'ind_to_uae' => $price_uae,
                'ind_to_sg' => $price_singapore,
                'ind_to_sa' => $price_saudi,
                'weight' => $packet_weight_kg
            ];
            $pricing_in[] = [...$asinDetails, ...$destination_price];
        }
        $count1++;
    }
    if ($country_code_lr == 'us') {

        PricingUs::upsert($pricing_us, 'unique_asin',  [
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
    }
});

// uncomment this one it is commented
// include_route_files(__DIR__ . '/pms/');

