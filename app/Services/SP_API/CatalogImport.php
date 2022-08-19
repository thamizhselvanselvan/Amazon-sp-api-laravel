<?php

namespace App\Services\SP_API;

use helpers;
use Exception;
use RedBeanPHP\R as R;
use App\Models\Aws_credentials;
use SellingPartnerApi\Endpoint;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogItemsV0Api;

class CatalogImport
{
    use ConfigTrait;

    public function amazonCatalogImport()
    {
        $startTime = startTime();

        Log::warning("warning from handle function");

        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password); // Log::warning($datas[0]->asin);
        // exit;
        $datas = AsinSource::limit(100)->offset(1400)->get();

        // dd($datas);

        Log::warning("success");

        foreach ($datas as $data) {
            // Log::info('AWS Auth Code - '. $data['aws']['auth_code']);
// dd($data->asin);
            $asin = $data->asin;
            // dd($data);
            // $asin = 'B000R1RKVY';
            // Log::info($asin);

            // $country_code = $data['source'];
            $aws_key = '';
            $auth_code = '';
            $country_code = 'US';
            // $auth_code = $data['aws']['auth_code'];
            // $aws_key = $data['aws']['id'];
            $item_condition = 'New';

            $marketplace_id = $this->marketplace_id($country_code);


            // $config = $this->config($aws_key, $country_code, $auth_code);
            $token = "Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
            $marketplace = 'ATVPDKIKX0DER'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
            $endpoint = Endpoint::NA;

            $config = new Configuration([
                "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
                "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
                "lwaRefreshToken" => $token,
                "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
                "awsSecretAccessKey" => "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",
                "endpoint" => $endpoint,  // or another endpoint from lib/Endpoints.php
                "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
            ]);

            $apiInstance = new CatalogItemsV0Api($config);

            // $apiInstancePricing = new ProductPricingApiProduct($config);
            $item_type = 'Asin';
            $asins = array($asin);
            try {
                // Log::warning("success1");
                $result = $apiInstance->getCatalogItem($marketplace, $asin);
                // po($result);
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
                        } 
                        elseif(is_string($data)) 
                        {
                            $productcatalogs->{$key} = ($data);
                        }
                        else{
                            $productcatalogs->{$key} = json_encode($data);
                            // $value [][$key] = ($data);
                        }
                    }
                    echo $asin;
                    echo "<hr>";
                    R::store($productcatalogs);
                } else {

                    Log::info($asin);
                }
            } catch (Exception $e) {

                echo $e . '<hr>';
                Log::alert($e);
            }
        }

        $endTime = endTime($startTime);
        Log::alert($endTime);
    }
}
