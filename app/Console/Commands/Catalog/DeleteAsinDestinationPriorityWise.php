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
        foreach($destinations as $destination)
        {
            $seller_destination = buyboxCountrycode();
            $country_code = strtolower($destination);
            $asin_destination = "asin_destination_${country_code}s";
            $product_table = "bb_product_${country_code}s";

            $table_name = table_model_create(country_code:$country_code, model:'Asin_destination', table_name:'asin_destination_');
            $destination_data = $table_name->where($asin_destination.'.priority', $priority)
            ->join('mosh_bb.'.$product_table, $asin_destination.'.asin', '=', $product_table.'.asin1')->get();

            foreach($destination_data as $desti_value)
            {
                $bb_product = table_model_set($country_code, 'BB_Product', 'product');
                $bb_product->where('asin1', $desti_value->asin)
                ->where($bb_product->getTable().'.seller_id', $seller_destination[$destination])
                ->delete();
            }
            $table_name->where('priority', $priority)->delete();
        }
    }
}
