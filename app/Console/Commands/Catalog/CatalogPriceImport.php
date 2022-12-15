<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use Illuminate\Support\Facades\Log;
use App\Models\Catalog\AsinDestination;
use App\Services\Catalog\PriceConversion;
use App\Services\Catalog\BuyBoxPriceImport;
use AmazonPHP\SellingPartner\Model\ProductPricing\BuyBoxPriceType;

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
    protected $signature = 'mosh:Catalog-price-import-bb-us';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import catalog from buy box for US';

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
        //Process Management start
        $process_manage = [
            'module'             => 'Catalog_price_bb_us',
            'description'        => 'Import catalog US price from bb table',
            'command_name'       => 'mosh:Catalog-price-import-bb-us',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];
        // $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end

        // $source = [
        //     'US' => 40,
        //     'IN' => 39
        // ];

        $country_code = 'US';
        $seller_id = '40';
        $limit = 3000;

        $buy_box_price = new BuyBoxPriceImport();
        $buy_box_price->fetchPriceFromBB($country_code, $seller_id, $limit);

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
        Log::notice($pm_id . '=> mosh:Catalog-price-import-bb-us');
    }
}
