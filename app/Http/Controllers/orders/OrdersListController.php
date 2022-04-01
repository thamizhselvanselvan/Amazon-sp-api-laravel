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
use RedBeanPHP\R as R;

class OrdersListController extends Controller
{
    public function index()
    {
        return view('orders.listorders.index');
    }

    public function GetOrdersList()
    {
        $host = config('app.host');
        $dbname = config('app.database');
        $port = config('app.port');
        $username = config('app.username');
        $password = config('app.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        echo 'Orders API/ getOrders ';
        //IN marketplace and token
        $token = "Atzr|IwEBIG3zt3kKghE3Bl56OEGAxxeodmEzfaMAnMl0PivBlfumR8224Adu9lb33DKLEvHD6OBwdIBkaVlIZ5L2axypPm-LLuKPabvUCmRZ6F6C8KZKBJYS2u1sJVqzMxxoFSs6DTFLMxx8WBVXY395aKUzK3plz3-ttDN-YUGjiKR9-kFhLek1ZdjxwTQkvUdWdfpuDtcnW0veAPS0JUHVwTN39hpwJtPXm98XwD-wEe16n9qoWoak-UvtuML8irbdUdATSA4FLSX08H2V7SFAjdktXEW13v6gBs3xfCYn_w9Y4H29K5i5_vkQyiqj0j1FMK0nmtU";
        $marketplace_ids = ['A21TJRUUN4KGV']; // string | A marketplace identifier. Specifies the marketplace for which prices are returned. 
        $endpoint = Endpoint::EU;
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
        $createdAfter = now()->subDays(1)->toISOString();
        $lastUpdatedBefore = now()->toISOString();

        echo "<pre>";
        try {
            $results = $apiInstance->getOrders($marketplace_ids, $createdAfter)->getPayload()->getOrders();

            $results = json_decode(json_encode($results));
            // print_r($results);

            foreach($results as $key => $result){
                $orders = R::dispense('orders');
                // $orders
            }
        } catch (Exception $e) {
            echo 'Exception when calling OrdersApi->getOrders: ', $e->getMessage(), PHP_EOL;
        }

        //API will hit here and records will be save into DB

    }
}
