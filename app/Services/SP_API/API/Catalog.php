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
    public function index($datas, $seller_id)
    {
        $host = config('database.connections.seller.host');
        $dbname = config('database.connections.seller.database');
        $port = config('database.connections.seller.port');
        $username = config('database.connections.seller.username');
        $password = config('database.connections.seller.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        foreach ($datas as $value) {
            $asin = $value->asin;
            $country_code = $value->source;
            $seller_id = $value->seller_id;

            $seller_detilas = Aws_credential::where('seller_id', $seller_id)->get();
            $auth_code = ($seller_detilas[0]->auth_code);
            $token = "Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
            $this->getCatalog($country_code, $token, $asin, $seller_id);
        }
    }

    public function getCatalog($country_code, $auth_code, $asin, $seller_id)
    {
        $config = $this->config(Null, $country_code, $auth_code);
        $apiInstance = new CatalogItemsV0Api($config);
        $marketplace = $this->marketplace_id($country_code);
        $country_code = '';

        try {
            $result = $apiInstance->getCatalogItem($marketplace, $asin);
            $result = json_decode(json_encode($result));
            if (isset(($result->payload->AttributeSets[0]))) {

                $result = (array)($result->payload->AttributeSets[0]);
                $productcatalogs = R::dispense('amazonseller');

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
                        // $value [][$key] = ($data);
                    }
                }
                R::store($productcatalogs);
            } else {
                Log::info($asin);
            }
            
            AsinMasterSeller::where('status', 0)
                ->where('asin', $asin)
                ->update(['status' => 1]);

        } catch (Exception $e) {
            Log::alert($e);
        }
    }
}
