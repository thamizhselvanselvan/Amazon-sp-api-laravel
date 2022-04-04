<?php

namespace App\Http\Controllers\orders;

use Exception;
use Carbon\Carbon;
use RedBeanPHP\R as R;
use AWS\CRT\HTTP\Response;
use Hamcrest\Type\IsObject;
use Illuminate\Http\Request;
use SellingPartnerApi\Endpoint;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use Yajra\DataTables\Contracts\DataTable;

class OrdersListController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::select('select amazon_order_identifier,purchase_date,last_update_date,order_status,order_total,number_of_items_shipped from orders');
            // dd($data);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('order_total', function ($order_total) {
                    $price = '';
                    $price = json_decode($order_total->order_total);
                    if (isset($price->Amount) && isset($price->CurrencyCode)) {

                        $price = $price->Amount . ' [' . $price->CurrencyCode . ']';
                    }
                    return $price;
                })
                ->rawColumns(['order_total'])
                ->make(true);;
        }
        return view('orders.listorders.index');
    }

    public function GetOrdersList()
    {
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        // echo 'Orders API/ getOrders ';
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

        // echo "<pre>";
        try {
            $results = $apiInstance->getOrders($marketplace_ids, $createdAfter)->getPayload()->getOrders();

            $results = json_decode(json_encode($results));
            // print_r($results);
            $orders = '';
            foreach ($results as $resultkey => $result) {

                // print_r((array)$result);
                $orders = R::dispense('orders');
                // $orders->book = 'Test';
                // $orders->auth = 'amit';
                // R::store($orders);
                // exit;
                foreach ((array)$result as $detailsKey => $details) {
                    // dd($details);
                    $detailsKey = lcfirst($detailsKey);


                    // $orders->$detailsKey = $details;
                    if (is_Object($details)) {

                        $orders->{$detailsKey} = json_encode($details);
                        // print_r($details);
                    } else if (is_array($details)) {

                        $orders->{$detailsKey} = json_encode($details);
                        // print_r($details);
                    } else {
                        if ($detailsKey == 'amazonOrderId') {
                            $orders->amazon_order_identifier = $details;
                        } else if ($detailsKey == 'marketplaceId') {

                            $orders->marketplace = $details;
                        } else {
                            $orders->{$detailsKey} = (string)$details;
                        }
                        // print_r($details);
                    }
                }
                R::store($orders);
            }
        } catch (Exception $e) {
            echo 'Exception when calling OrdersApi->getOrders: ', $e->getMessage(), PHP_EOL;
        }

        return redirect()->back();
        //API will hit here and records will be save into DB

    }
}
