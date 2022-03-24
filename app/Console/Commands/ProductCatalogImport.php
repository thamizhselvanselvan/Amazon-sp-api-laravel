<?php

namespace App\Console\Commands;

use PDO;
use Exception;
use PDOException;
use RedBeanPHP\R as R;
use App\Models\asinMaster;
use Illuminate\Console\Command;
use App\Jobs\AmazonCatalogImport;
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
        // $datas = asinMaster::with(['aws'])->limit(100)->get();

        // foreach($datas as $data){
        //     // $asin = $data['asin'];
        //     // $country_code = $data['source'];
        //     // $auth_code = $data['aws']['auth_code'];
        //     // $aws_key = $data['aws']['id'];

        //     // AmazonCatalogImport::dispatch(
        //     //     [
        //     //         'asin' => $asin,
        //     //         'country_code' => $country_code,
        //     //         'auth_code' => $auth_code,
        //     //         'aws_key' => $aws_key,
        //     //     ]
        //     // );
        // }
        $amazonCatalogsImport = new CatalogImport();
        $amazonCatalogsImport->amazonCatalogImport();
    }
}
