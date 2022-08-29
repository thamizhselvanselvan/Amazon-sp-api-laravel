<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use Illuminate\Support\Facades\Log;
use App\Models\Catalog\AsinDestination;
use App\Services\Catalog\PriceConversion;

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
    }
}
