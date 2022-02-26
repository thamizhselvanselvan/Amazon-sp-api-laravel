<?php

namespace App\Console\Commands;

use PDO;
use Exception;
use PDOException;
use RedBeanPHP\R as R;
use App\Models\asinMaster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Config\ConfigTrait;
use App\Services\SP_API\CatalogImport;
use SellingPartnerApi\Api\CatalogItemsV0Api as CatalogItemsV0ApiPackage;
class ProductCatalogImport extends Command
{
    use ConfigTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:catalog-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SP API catalog import through ASIN';

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
        $amazonCatalogsImport = new CatalogImport();
        $amazonCatalogsImport->amazonCatalogImport();
    }
}
