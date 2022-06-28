<?php

namespace App\Console\Commands;

use Exception;
use RedBeanPHP\R;
use App\Models\Aws_credential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Catalog;
use SellingPartnerApi\Api\OrdersApi;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use App\Jobs\Seller\Seller_catalog_import_job;

class SellerOrdersItemImport extends Command
{
    use ConfigTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:seller-order-item-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import seller order item import from amazon sp api';

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
