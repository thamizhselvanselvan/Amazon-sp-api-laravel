<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Services\Catalog\BuyBoxPriceImport;

class CatalogPriceImportSA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Catalog-price-import-bb-sa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import catalog price form bb table for SA';

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

        $country_code = 'SA';
        $seller_id = '50';
        $limit = 1000;

        $buy_box_price = new BuyBoxPriceImport();
        $buy_box_price->fetchPriceFromBB($country_code, $seller_id, $limit);
    }
}
