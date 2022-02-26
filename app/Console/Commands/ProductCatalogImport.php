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
        Log::warning("warning from handle function");
        $connection = config('app.connection');
        $host = config('app.host');
        $dbname = config('app.database');
        $port = config('app.port');
        $username = config('app.username');
        $password = config('app.password');

        Log::warning('host->' . $host . ',port->'. $port .',dbname->' . $dbname . ',username->' . $username . 'password->' . $password);
        R::setup('mysql: host=' . $host . '; dbname=' . $dbname . ';port=' . $port, $username, $password);
        Log::alert("working");

        $datas = asinMaster::with(['aws'])->limit(10)->get();
        
        foreach ($datas as $data) {
            

            $asin = $data['asin'];
            $country_code = $data['destination_1'];
            $auth_code = $data['aws']['auth_code'];
            $aws_key = $data['aws']['id'];
            $marketplace_id = $this->marketplace_id($country_code);

            $config = $this->config($aws_key, $country_code, $auth_code);

            $apiInstance = new CatalogItemsV0ApiPackage($config);
            $marketplace_id = $this->marketplace_id($country_code);
            Log::warning("try to get catalog data");
            try {
                $result = $apiInstance->getCatalogItem($marketplace_id, $asin);

                $result = json_decode(json_encode($result));

                $result = (array)($result->payload->AttributeSets[0]);

                $productcatalogs = R::dispense('amazon');
                Log::alert("amazon table created");
                $value = [];
                $productcatalogs->asin = $asin;
                $productcatalogs->destination = $country_code;

                foreach ($result as $key => $data) {
                    $key = lcfirst($key);
                    if (is_object($data)) {

                        $productcatalogs->{$key} = json_encode($data);
                    } else {
                        $productcatalogs->{$key} = json_encode($data);
                        // $value [][$key] = ($data);
                    }
                }

                R::store($productcatalogs);
                Log::alert('product catalog saved');
                
            } catch (Exception $e) {
                Log::notice($e->getMessage());
            }
           
        }
    }
}
