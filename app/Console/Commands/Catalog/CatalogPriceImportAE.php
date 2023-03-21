<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use App\Services\Catalog\BuyBoxPriceImport;
use App\Services\Catalog\ImportPriceFromBuyBox;

class CatalogPriceImportAE extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Catalog-price-import-bb-ae';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import catalog price form bb table for AE';

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

        $country_code = 'AE';
        $seller_id = '38';
        $limit = 1000;

        $process_manage = [
            'module'             => 'Catalog Price Import',
            'description'        => "Fetch AE catalog price from BuyBox",
            'command_name'       => 'mosh:Catalog-price-import-bb-ae',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $buy_box_price = new ImportPriceFromBuyBox();
        $buy_box_price->GetPriceFromBuyBox($country_code);

        // $buy_box_price = new BuyBoxPriceImport();
        // $buy_box_price->fetchPriceFromBB($country_code, $seller_id, $limit);

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
