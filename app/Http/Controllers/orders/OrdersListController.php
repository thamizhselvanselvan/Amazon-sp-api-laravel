<?php

namespace App\Http\Controllers\orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use App\Services\Config\ConfigTrait;
use Carbon\Carbon;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\OrdersApi;

class OrdersListController extends Controller
{
    public function index()
    {
        return view('orders.listorders.index');
    }

    public function GetOrdersList()
    {
        echo 'Orders API/ getOrders ';
        //Us marketplace and token
        $token = "Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
        $marketplace_ids = ['ATVPDKIKX0DER']; // 
        $endpoint = Endpoint::NA;
        $config = new Configuration([
            "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
            "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
            "lwaRefreshToken" => $token,
            "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
            "awsSecretAccessKey" => "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",
            "endpoint" => $endpoint,
            "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role' // or another endpoint from lib/Endpoints.php
        ]);
        $apiInstance = new OrdersApi($config);
        $createdAfter =  Carbon::now();
        $createdBefore = '2022-04-01 12:00:00.000';

        try {
            $result = $apiInstance->getOrders($marketplace_ids, $createdAfter);
            po($result);
        } catch (Exception $e) {
            echo 'Exception when calling OrdersApi->getOrders: ', $e->getMessage(), PHP_EOL;
        }

        //API will hit here and records will be save into DB

    }
}
