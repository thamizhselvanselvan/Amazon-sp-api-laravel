<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SearchAsinDeleteFromDestinationAndBuyBox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:search-asin-delete-bb-destination {priority} {source} {asins}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete asins from Asin_destination, buybox-product-table and buybox-offers-table';

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
        $priority = $this->argument('priority');
        $source = $this->argument('source');
        $Asins = explode(',', $this->argument('asins'));

        $buybox_offer_table = "product_aa_custom_p${priority}_${source}_offer";
        $bb_product_table = "product_aa_custom_p${priority}_${source}";
        $cat_table = "asin_destination_${source}s";
        $dbname = config('database.connections.buybox.database');

        foreach ($Asins as $key => $asin) {
            $modal_table = table_model_create(country_code: $source, model: 'Asin_destination', table_name: 'asin_destination_');
            $modal_table->where('asin', $asin)->delete();
            $bb_product = table_model_set(country_code: $source, model: 'bb_product_aa_custom', table_name: $bb_product_table);
            $bb_product->where('asin1', $asin)->delete();
            $bb_product_lowest_price = table_model_set(country_code: $source, model: 'bb_product_aa_custom_offer', table_name: $buybox_offer_table);
            $bb_product_lowest_price->where('asin', $asin)->delete();
        }
    }
}
