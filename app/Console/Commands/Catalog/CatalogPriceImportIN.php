<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Services\Catalog\BuyBoxPriceImport;

class CatalogPriceImportIN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Catalog-price-import-bb-in';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import catalog price form bb table for IN';

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
        // $source = [
        //     'US' => 40,
        //     'IN' => 39
        // ];

        $country_code = 'IN';
        $seller_id = '39';
        $limit = 4000;

        $buy_box_price = new BuyBoxPriceImport();
        $buy_box_price->fetchPriceFromBB($country_code, $seller_id, $limit);
    }
}
