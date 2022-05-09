<?php

namespace App\Services\SP_API;

use helpers;
use Exception;
use RedBeanPHP\R as R;
use App\Models\Asin_master;
use App\Models\Aws_credentials;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogItemsV0Api as CatalogItemsV0ApiProduct;
use SellingPartnerApi\Api\ProductPricingApi as ProductPricingApiProduct;

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
        $datas = Asin_Master::limit(15)->offset(0)->get();

        $datas = [
            "B07C84Y9N3",
            "B00CRMMGX8",
            "B076PW5ZLQ",
            "B07BBVMWSG",
            "B07B2KXRH7",
            "B006UGY5OU",
            "B0017O6LLU",
            "B079SVN629",
            "B08P2BJTHR",
            "B0791D9YSW",
            "B078V69RY4",
            "B007CKI0PI",
            "B00AQ5O2Z8",
            "B077H43CJR",
            "B078YP2GSH",
            "B078C7F21X",
            "B01EY6JZCY",
            "B00WG4S3BQ",
            "B00O1R4BXK",
            "B071ZHLBDW",
            "B076936NQJ",
            "B077B7BQS5",
            "B000ZQ4NUC",
            "B00H3HLD0Y",
            "B076Q9285S",
            "B00ISZXHNG",
            "B0711T81MY",
            "B075QBGXN9",
            "B07CCYLXCD",
            "B07GBSR53R",
            "B073XVQF7K",
            "B0748KNZYB",
            "B00JQA0RVC",
            "B07PLMWC2C",
            "B07252Y92P",
            "B002KCC9LY",
            "B00NH0VBUI",
            "B0711C8P46",
            "B071NTL796",
            "B07FQ9FG7S",
            "B01MR67XWC",
            "B08QB2BMX8",
            "B08L25L3TK",
            "B06ZYR75RQ",
            "B07FKTJMC9",
            "B078HTPGFJ",
            "B005MQNTGE",
            "B06XPCSDDN",
            "B01NBVK3G3",
            "B073V9VG8M",
            "B0752X59R3",
            "B07CKCQQGS",
            "B07FN4K7M8",
            "B087JX2LDS",
            "B002JK4KXW",
            "B00NFVIWQA",
            "B07RCCPLY5",
            "B06XQ11WHX",
            "B000R4PW8E",
            "B01N6K04Y4",
            "B000BREP3K",
            "B01N39KJ67",
            "B01MXOJL3H",
            "B01CGBT0GK",
            "B005NB03US",
            "B01MQ14D9M",
            "B00M34QG5O",
            "B07G348RJH",
            "B01N0XPX4Q",
            "B01MCX2B3M",
            "B01MYY13TF",
            "B01MZALB27",
            "B00QH1CP18",
            "B00X7CJ55E",
            "B01257UAWS",
            "B0077T4T86",
            "B00YSW1Q7C",
            "B0733KBF7H",
            "B08V22ZLQ9",
            "B08MQN2TYP",
            "B019GBKAC6",
            "B00WYXN9M2",
            "B009UK21OY",
            "B07VWNCVX9",
            "B06WP5P3C9",
            "B00JJ165MS",
            "B00MPRQVRM",
            "B081NJ2GZ9",
            "B00O4AO468",
            "B00W2BWX46",
            "B0151GI5VI",
            "B08QD9HSFX",
            "B00DF6XYEU",
            "B071RS2SR7",
            "B013IJPTFK",
            "B07G9LQ69N",
            "B00N549N7Y",
            "B00VSHW62O",
            "B00AQSCKH2",
            "B00KJJ84X2"
        ];

        // dd($datas);

        Log::warning("success");

        foreach ($datas as $data) {
            // Log::info('AWS Auth Code - '. $data['aws']['auth_code']);

            $asin = $data;
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

            $apiInstance = new CatalogItemsV0ApiProduct($config);

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
                    $productcatalogs = R::dispense('amazonusdata');

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
                    // $result = $apiInstancePricing->getCompetitivePricing($marketplace_id, $item_type, $asins)->getPayload();
                    // $result = json_decode(json_encode($result));

                    // if (isset($result[0]->Product->CompetitivePricing->CompetitivePrices[0]->Price->LandedPrice)) {

                    //     $pricing = $result[0]->Product->CompetitivePricing->CompetitivePrices[0]->Price->LandedPrice;
                    //     $currencyCode =  $pricing->CurrencyCode;
                    //     $Amount = $pricing->Amount;

                    //     $productcatalogs->currencyCode = $currencyCode;
                    //     $productcatalogs->amount = $Amount;
                    // }
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
