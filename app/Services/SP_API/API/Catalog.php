<?php

namespace App\Services\SP_API\API;

use App\Models\Admin\BB\BB_User;
use App\Models\Aws_credential;
use Exception;
use RedBeanPHP\R;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use App\Models\seller\AsinMasterSeller;
use App\Services\SP_API\Config\ConfigTrait;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Api\CatalogItemsV0Api;


class Catalog
{

    use ConfigTrait;

    public function getCatalog()
    {
        $startTime = startTime();
        $host = config('database.connections.seller.host');
        $dbname = config('database.connections.seller.database');
        $port = config('database.connections.seller.port');
        $username = config('database.connections.seller.username');
        $password = config('database.connections.seller.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password); // Log::warning($datas[0]->asin);
        
        $login_user = Auth::user();
        $email = $login_user->email;
        $login_id = $login_user->id;
        
        $DB_user = BB_User::where('email', $email)->get();
        $seller_id = $DB_user[0]->id;
        $datas = AsinMasterSeller::limit(100)->offset(1400)->where('status', 0)->where('seller_id', $login_id)->get();
       
        foreach ($datas as $value) {

            $asin = $value->asin;
            $seller_id = $value->seller_id;
            echo $seller_id;
        }
        $seller_detilas = Aws_credential::where('seller_id', $seller_id)->get();
        exit;
        $asin = '';
        $config = $this->config(1, 1, 1);
        $apiInstance = new CatalogItemsV0Api($config);
        $marketplace = $this->marketplace_id('US');
        $country_code = '';

        try {
            $result = $apiInstance->getCatalogItem($marketplace, $asin);
            $result = json_decode(json_encode($result));

            if (isset(($result->payload->AttributeSets[0]))) {

                $result = (array)($result->payload->AttributeSets[0]);
                $productcatalogs = R::dispense('bookswagon');

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
                        // $value [][$key] = ($data);
                    }
                }
                R::store($productcatalogs);
            } else {

                // Log::info($asin);
            }
        } catch (Exception $e) {

            echo $e . '<hr>';
            Log::alert($e);
        }
    }
}
