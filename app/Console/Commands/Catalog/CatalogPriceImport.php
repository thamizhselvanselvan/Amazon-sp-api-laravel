<?php

namespace App\Console\Commands\Catalog;

use App\Models\Catalog\Asin_master;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CatalogPriceImport extends Command
{
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

        $source = buyboxCountrycode();

        $chunk = 1000;

        foreach ($source as $country_code => $seller_id) {

            $country_code_lr = strtolower($country_code);

            $product_lp = 'bb_product_lp_seller_detail_' . $country_code_lr . 's';
            $product = 'bb_product_' . $country_code_lr . 's';

            Asin_master::where('source', $country_code)
                ->chunk($chunk, function ($data) use ($seller_id, $product, $product_lp) {

                    foreach ($data as $value) {
                        $a = $value['asin'];
                        $asin_array[] = "'$a'";
                    }

                    $asin = implode(',', $asin_array);

                    $data = DB::connection('buybox')
                        ->select("SELECT PPO.asin,
                    GROUP_CONCAT(PPO.is_buybox_winner) as is_buybox_winner,
                    group_concat(PPO.listingprice_amount) as listingprice_amount
                    FROM $product_lp as PPO
                        AND PPO.asin IN ($asin)
                        GROUP BY PPO.asin 
                    ");
                });
        }
    }
}
