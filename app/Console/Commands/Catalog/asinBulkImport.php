<?php

namespace App\Console\Commands\Catalog;

use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Console\Command;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class asinBulkImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:asin-import {user_id} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Asin from CSV';

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
        //source and seller id

        $user_id = $this->argument('user_id');

        $path = 'AsinMaster/asin.csv';

        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        $source = buyboxCountrycode();
        $asin = [];
        $count = 0;
        $country_code = '';
        $seller_id = '';

        foreach ($csv as $key => $record) {
            $country_code = strtoupper($record['Source']);
            $seller_id = $source[$country_code];
            $asin = $record['ASIN'];

            $asin_details[] = [

                'asin' => $asin,
                'user_id' => $user_id,
                'source' => $record['Source'],
            ];

            $product[] = [
                'seller_id' => $seller_id,
                'active' => 1,
                'asin1' => $asin,
            ];

            $product_lowest_price[] = [
                'asin' => $asin,
                'import_type' => 'Seller',
            ];

            if ($count == 1000) {

                AsinSource::upsert($asin_details, ['user_asin_source_unique'], ['source']);
                $bb_product = table_model_set($country_code, 'BB_Product', 'product');
                $bb_product->insert($product);

                $bb_product_lowest_price = table_model_set(country_code: $country_code, model: 'BB_Product_lowest_price_offer', table_name: 'product_lp_offer');
                $bb_product_lowest_price->upsert($product_lowest_price, ['asin'], ['asin']);
                $count = 0;
                $asin = [];
                $product = [];
                $product_lowest_price = [];
            }
            $count++;
        }

        AsinSource::upsert($asin_details, ['user_asin_source_unique'], ['source']);

        $bb_product = table_model_set($country_code, 'BB_Product', 'product');
        $bb_product->insert($product);

        $bb_product_lowest_price = table_model_set(country_code: $country_code, model: 'BB_Product_lowest_price_offer', table_name: 'product_lp_offer');
        $bb_product_lowest_price->upsert($product_lowest_price, ['asin'], ['asin']);

        Log::warning(" asin import successfully");

        Log::info('Catalog importing');

        $asins = AsinSource::where('status', '=', 0)->get(['asin', 'source', 'user_id']);
        $count = 0;
        $asin_source = [];
        $class = 'catalog\\AmazonCatalogImport';
        foreach ($asins as $asin) {

            if ($count == 10) {
                jobDispatchFunc($class, $asin_source, 'catalog');
                $asin_source = [];
                $count = 0;
            } else {

                $asin_source[] = [
                    'asin' => $asin->asin,
                    'source' => $asin->source,
                    'seller_id' => $asin->user_id
                ];
                $count++;
            }
        }
        jobDispatchFunc($class, $asin_source, 'catalog');
    }
}
