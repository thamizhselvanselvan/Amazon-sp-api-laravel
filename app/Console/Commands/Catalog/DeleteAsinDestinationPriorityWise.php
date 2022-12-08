<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteAsinDestinationPriorityWise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Asin-destination-delete-priority-wise {priority} {--destinations=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = " Delete AsinDestination's ASIN according to priority";

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
        $destinations = explode(',', $this->option('destinations'));
        // log::alert($destinations);
        $dbname = config('database.connections.buybox.database');
        foreach ($destinations as $destination) {
            $seller_destination = buyboxCountrycode();
            $country_code = strtolower($destination);
            $asin_destination = "asin_destination_${country_code}s";
            $product_table = "bb_product_aa_custom_p${priority}_${country_code}s";

            $table_name = table_model_create(country_code: $country_code, model: 'Asin_destination', table_name: 'asin_destination_');
            // $table_name->select('id', 'asin')->where("${asin_destination}" . '.priority', $priority)->chunkById(5000, function ($records) use ($seller_destination, $country_code, $priority, $destination, $table_name) {
            //     $asins = $records->toArray();
            //     foreach ($asins as $asin) {

            //         $asin1[] = $asin['asin'];
            //     }

            //     $bb_product = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom', table_name: "product_aa_custom_p${priority}_${country_code}");
            //     $bb_product_table = $bb_product->whereIn('asin1', $asin1)
            //         ->where($bb_product->getTable() . '.seller_id', $seller_destination[$destination])
            //         ->delete();

            //     // $bb_product_offer_table = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom_offer', table_name: "product_aa_custom_p${priority}_${country_code}_offer");
            //     // $offer_table = $bb_product_offer_table->whereIn('asin', $asin1)->delete();
            // });
            $bb_product = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom', table_name: "product_aa_custom_p${priority}_${country_code}");
            $bb_product->truncate();
            $bb_product_offer_table = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom_offer', table_name: "product_aa_custom_p${priority}_${country_code}_offer");
            $bb_product_offer_table->truncate();
            $seller_table_name = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom_offer', table_name: "product_aa_custom_p${priority}_${country_code}_seller_detail");
            $seller_table_name->truncate();

            $table_name->where('priority', $priority)->delete();
        }

        commandExecFunc("mosh:catalog-dashboard-file");
    }
}
