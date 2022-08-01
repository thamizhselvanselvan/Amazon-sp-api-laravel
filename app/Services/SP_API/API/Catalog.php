<?php

namespace App\Services\SP_API\API;

use Exception;
use RedBeanPHP\R;
use App\Models\Mws_region;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use App\Models\Admin\BB\BB_User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use App\Models\seller\AsinMasterSeller;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\CatalogItemsV0Api;


class Catalog
{
    use ConfigTrait;
    
    public function index($datas, $seller_id = NULL, $type)
    {
        
        //$type = 1 for seller, 2 for Order, 3 for inventory
        $host = config('database.connections.catalog.host');
        $dbname = config('database.connections.catalog.database');
        $port = config('database.connections.catalog.port');
        $username = config('database.connections.catalog.username');
        $password = config('database.connections.catalog.password');

        if (!R::testConnection('catalog', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
            R::addDatabase('catalog', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            R::selectDatabase('catalog');
        }

        if ($type == 1 || $type == 4) {
            // Log::notice($type);
            foreach ($datas as $value) {
                $asin = NULL;
                $value = (object)$value;
                $asin = $value->asin;
                $country_code = $value->source;

                $mws_region = Mws_region::with('aws_verified')->where('region_code', $country_code)->get()->first();
                $token = ($mws_region['aws_verified']['auth_code']);

                $seller_id = $value->seller_id;
                $country_table = strtolower($country_code);
                $countrywise_table = 'catalog'.$country_table.'s';
                $databases = DB::connection('catalog')->select('SHOW TABLES');
                $check = [];
                foreach($databases as $key => $database)
                {   
                    $table = $database->Tables_in_mosh_catalog;
                    if($table == $countrywise_table)
                    {
                        // echo 'working'.'<br>';
                        // Log::notice($countrywise_table);
                        $check = DB::connection('catalog')->select("SELECT asin from $countrywise_table where asin = '$asin'");
                    } 
                    // else{
                        
                        if (count($check) <= 0) {
        
                            $aws_id = NULL;
                            $this->getCatalog($country_code, $token, $asin, $seller_id, $type, $aws_id);
                        }
                        
                    // }
                }
            }
        } elseif ($type == 2) {

            foreach ($datas as $value) {
                $asin = $value['asin'];
                $country_code = $value['country_code'];
                $aws_id = $value['aws_id'];
                $auth_code = NULL;
                $seller_id = NULL;
                $this->getCatalog($country_code, $auth_code, $asin, $seller_id, $type, $aws_id);
            }
            // return true;
        }
    }

    public function getCatalog($country_code, $auth_code, $asin, $seller_id, $type, $aws_id)
    {
        $config = $this->config($aws_id, $country_code, $auth_code);
        $apiInstance = new CatalogItemsV0Api($config);
        $marketplace = $this->marketplace_id($country_code);
        
        $country_code = strtolower($country_code);
        $table_name = 'catalog'.$country_code.'s';
        
        $seller_id = $aws_id?$aws_id:$seller_id; 
        try {
            $result = $apiInstance->getCatalogItem($marketplace, $asin);
            $result = json_decode(json_encode($result));
            if (isset(($result->payload->AttributeSets[0]))) {

                $result = (array)($result->payload->AttributeSets[0]);
                $productcatalogs = R::dispense($table_name);

                // Log::alert($productcatalogs);
                // exit;

                $productcatalogs->seller_id = $seller_id;
                $productcatalogs->asin = $asin;
                $productcatalogs->source = $country_code;

                foreach ($result as $key => $data) {
                    $key = lcfirst($key);
                    
                    if (is_object($data)) {

                        $productcatalogs->{$key} = json_encode($data);
                    } elseif (is_string($data)) {
                        $productcatalogs->{$key} = ($data);
                    } else {
                        $productcatalogs->{$key} = json_encode($data);
                        
                    }
                }
                R::store($productcatalogs);
            } else {
                Log::info($asin);
            }
            if ($type == 1) {
                //seller
                AsinMasterSeller::where('status', 0)
                    ->where('asin', $asin)
                    ->update(['status' => 1]);
            } elseif ($type == 2) {
                //order
                DB::connection('order')
                    ->update("UPDATE orderitemdetails SET status = '1' where asin = '$asin'");
            } elseif ($type == 3) {
                //inventory

            } elseif ($type == 4) {
                DB::connection('catalog')
                ->update("UPDATE asin_masters SET status = '1' WHERE status = '0'");
            }
        } catch (Exception $e) {
            Log::alert($e);
        }
    }
}
