<?php

namespace App\Console\Commands;

use Exception;
use RedBeanPHP\R;
use App\Models\asinMaster;
use Illuminate\Console\Command;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Api\CatalogItemsV0Api;
use Illuminate\Support\Facades\Log;

class ProductCatalogImport extends Command
{    use ConfigTrait;
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
        $username = config('app.username');
        $password = config('app.password');

        Log::warning("db configuration done");

        R::setup('mysql: host='.$host.'; dbname='.$dbname, $username, $password); 
        R::exec('TRUNCATE `productcatalogs`'); 
        
        Log::warning("productcatalogs table created");

        $datas = asinMaster::with(['aws'])->limit(10)->get();

        Log::warning('relation stablish b/w dependent table');

        foreach($datas as $data){

            $asin = $data['asin'];
            $country_code = $data['destination_1'];
            $auth_code = $data['aws']['auth_code'];
            $aws_key = $data['aws']['id'];
            $marketplace_id = $this->marketplace_id($country_code);
    
            $config = $this->config($aws_key, $country_code, $auth_code);
    
            $apiInstance = new CatalogItemsV0Api($config);
            $marketplace_id = $this->marketplace_id($country_code);
            Log::warning("try to get catalog data");
            try {
                $result = $apiInstance->getCatalogItem($marketplace_id, $asin);
                
                $result = json_decode(json_encode($result));
                
                $result = (array)($result->payload->AttributeSets[0]);
                
                $productcatalogs = R::dispense('productcatalogs');
            
                $value = [];
                $productcatalogs->asin = $asin;
                
                foreach ($result as $key => $data){
                    $key = lcfirst($key);
                    if(is_object($data)){
            
                        $productcatalogs->{$key} = json_encode($data);
                    }
                    else
                    {
                        $productcatalogs->{$key} = json_encode($data);
                        // $value [][$key] = ($data);
                    }
    
                }
                
                R::store($productcatalogs);
                
                
            } catch (Exception $e) {
                echo 'Exception when calling CatalogItemsV0Api->getCatalogItem: ', $e->getMessage(), PHP_EOL;
            }
            
            
        }
    }
}
