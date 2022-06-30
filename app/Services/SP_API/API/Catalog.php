<?php

namespace App\Services\SP_API\API;

use Exception;
use RedBeanPHP\R;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use App\Models\Admin\BB\BB_User;
use App\Models\Mws_region;
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

        if ($type == 1) {
            foreach ($datas as $value) {
                $asin = NULL;
                $asin = $value->asin;
                $country_code = $value->source;

                $mws_region = Mws_region::with('aws_verified')->where('region_code', $country_code)->get()->first();
                $token = ($mws_region['aws_verified']['auth_code']);

                $seller = $value->seller_id;
                $check = DB::connection('catalog')->select("SELECT asin from catalog where asin = '$asin'");

                if (count($check) <= 0) {

                    $aws_id = NULL;
                    $this->getCatalog($country_code, $token, $asin, $seller, $type, $aws_id);
                }
            }
        } elseif ($type == 2) {

            foreach ($datas as $value) {
                $asin = $value['asin'];
                $country_code = $value['country_code'];
                $aws_id = $value['aws_id'];
                $auth_code = NULL;
                // $seller_detilas = Aws_credential::where('seller_id', $seller)->get();
                // $token = ($seller_detilas[0]->auth_code);
                // $token = "Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
                $this->getCatalog($country_code, $auth_code, $asin, $type, $aws_id);
            }
            // return true;
        }
    }

    public function getCatalog($country_code, $auth_code, $asin, $type, $aws_id)
    {
        $config = $this->config($aws_id, $country_code, $auth_code);
        $apiInstance = new CatalogItemsV0Api($config);
        $marketplace = $this->marketplace_id($country_code);
        $country_code = '';

        try {
            $result = $apiInstance->getCatalogItem($marketplace, $asin);
            $result = json_decode(json_encode($result));
            if (isset(($result->payload->AttributeSets[0]))) {

                $result = (array)($result->payload->AttributeSets[0]);
                $productcatalogs = R::dispense('catalog');

                $productcatalogs->seller_id = $aws_id;
                $productcatalogs->asin = $asin;
                $productcatalogs->source = $country_code;

                foreach ($result as $key => $data) {
                    $key = lcfirst($key);
                    // if($key == 'title')
                    // {
                    //     $productcatalogs->{$key} = $data;
                    // }
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

            }
        } catch (Exception $e) {
            Log::alert($e);
        }
    }
}
