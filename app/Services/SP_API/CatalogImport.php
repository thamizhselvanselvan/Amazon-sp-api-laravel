<?php

namespace App\Services\SP_API;

use helpers;
use Exception;
use RedBeanPHP\R as R;
use App\Models\asinMaster;
use App\Models\Aws_credentials;
use SellingPartnerApi\Endpoint;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogItemsV0Api as CatalogItemsV0ApiProduct;
use SellingPartnerApi\Api\ProductPricingApi as ProductPricingApiProduct;

class CatalogImport
{
    use ConfigTrait;

    public function amazonCatalogImport($asin, $country_code, $auth_code, $aws_key)
    {
        $startTime = startTime();

        Log::warning("warning from handle function");
        $connection = config('app.connection');
        $host = config('app.host');
        $dbname = config('app.database');
        $port = config('app.port');
        $username = config('app.username');
        $password = config('app.password');
        
        // $datas = asinMaster::with(['aws'])->limit(2000)->get();
        
        
            R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            // R::setup('mysql:host='.$host.';dbname='.$dbname.';port='.$port, $username, $password);
            Log::warning("success");
            
            // // foreach ($datas as $data) {
            //     Log::info('AWS - '. $data['aws']);
            //     Log::info('AWS Auth Code - '. $data['aws']['auth_code']);
                
                // $asin = $data['asin'];

                // $country_code = $data['source'];
                // $auth_code = $data['aws']['auth_code'];
                // $aws_key = $data['aws']['id'];
                // $item_condition = 'New';

                $marketplace_id = $this->marketplace_id($country_code);


                $config = $this->config($aws_key, $country_code, $auth_code);
                
                $apiInstance = new CatalogItemsV0ApiProduct($config);

                $apiInstancePricing = new ProductPricingApiProduct($config);
                $item_type = 'Asin';
                $asins = array($asin);
                try {
                    Log::warning("success1");
                    $result = $apiInstance->getCatalogItem($marketplace_id, $asin);
                    
                    $result = json_decode(json_encode($result));
                    
                    $result = (array)($result->payload->AttributeSets[0]);
                
                    $productcatalogs = R::dispense('amazon');
                    
                    $productcatalogs->asin = $asin;
                    $productcatalogs->source = $country_code;
                    
                    foreach ($result as $key => $data) {
                        $key = lcfirst($key);
                        if (is_object($data)) {
                            
                            $productcatalogs->{$key} = json_encode($data);
                        } else {
                            $productcatalogs->{$key} = json_encode($data);
                            // $value [][$key] = ($data);
                        }
                    }
                    $result = $apiInstancePricing->getCompetitivePricing($marketplace_id, $item_type, $asins)->getPayload();
                    $result = json_decode(json_encode($result));
                    
                    $pricing = $result[0]->Product->CompetitivePricing->CompetitivePrices[0]->Price->LandedPrice;
                    $currencyCode =  $pricing->CurrencyCode;  
                    $Amount = $pricing->Amount; 

                    $productcatalogs->currencyCode = $currencyCode;
                    $productcatalogs->amount = $Amount;

                    R::store($productcatalogs);
                
                } catch (Exception $e) {
                    Log::alert($e);
                }
                
            // }
        
         $endTime = endTime($startTime);
            Log::alert($endTime);
    }
}
