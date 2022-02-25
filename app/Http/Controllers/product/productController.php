<?php

namespace App\Http\Controllers\product;

use Exception;
use League\Csv\Reader;
use RedBeanPHP\R as R;
use League\Csv\Statement;
use App\Models\asinMaster;
use Illuminate\Http\Request;
use League\Csv\XMLConverter;
use App\Models\aws_credentials;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Concerns\ToArray;
use SellingPartnerApi\Api\CatalogItemsV0Api;


class productController extends Controller
{
    use ConfigTrait;
    public function index(){

        return view('product.index');
    }

public function fetchFromAmazon(){

    $datas = asinMaster::with(['aws'])->limit(100)->get();
    $connection = config('app.connection');
    $host = config('app.host');
    $dbname = config('app.database');
    $username = config('app.username');
    $password = config('app.password');

    R::setup('mysql: host='.$host.'; dbname='.$dbname, $username, $password); 
    R::exec('TRUNCATE `productcatalogs`'); 

    foreach($datas as $data){
        $asin = $data['asin'];
        $country_code = $data['destination_1'];
        $auth_code = $data['aws']['auth_code'];
        $aws_key = $data['aws']['id'];
        $marketplace_id = $this->marketplace_id($country_code);

        $config = $this->config($aws_key, $country_code, $auth_code);

        $apiInstance = new CatalogItemsV0Api($config);
        $marketplace_id = $this->marketplace_id($country_code);
    
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
    /*
    *
    * command start
    *
    */

    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

        Log::warning("asin production executed");

        $base_path = base_path();
        $command = "cd $base_path && php artisan pms:catalog-import > /dev/null &";
        exec($command);
        Log::warning("asin production command executed");
        
    } else {

        Log::warning("Export command executed local !");
        Artisan::call('pms:catalog-import');
        
    }
    // command end


     
    }
}
