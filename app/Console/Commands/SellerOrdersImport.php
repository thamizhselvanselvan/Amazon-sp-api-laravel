<?php

namespace App\Console\Commands;

use RedBeanPHP\R;
use Aws\AwsClient;
use App\Models\Aws_credential;
use Illuminate\Console\Command;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use App\Services\SP_API\Config\ConfigTrait;
use AmazonPHP\SellingPartner\Exception\Exception;

class SellerOrdersImport extends Command
{
    use ConfigTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:sellers-orders-import {seller-id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Seller orders from Amazon according to seller id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::alert('working');
        $seller_id = $this->argument('seller-id');

        $aws_data = Aws_credential::with('mws_region')->where('seller_id', $seller_id)->where('verified', 1)->get();
        Log::debug("Seller id".$seller_id);
        $awsId  = $aws_data[0]['id'];
        $awsAuth_code = $aws_data[0]['auth_code'];
        $awsCountryCode = $aws_data[0]['mws_region']['region_code'];
        
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        Log::debug($host, $dbname, $port, $username, $password);
        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        
        $config = $this->config($awsId, $awsCountryCode, $awsAuth_code);
        $marketplace_ids = $this->marketplace_id($awsCountryCode);
        $marketplace_ids= [$marketplace_ids];
        Log::critical($config);
        $apiInstance = new OrdersApi($config);
        $createdAfter = now()->subDays(1)->toISOString();
        $lastUpdatedBefore = now()->toISOString();

        try {
            $results = $apiInstance->getOrders($marketplace_ids, $createdAfter)->getPayload()->getOrders();

            $results = json_decode(json_encode($results));
            $orders = '';
            foreach ($results as $resultkey => $result) {
                Log::warning('foreach working');
                // print_r((array)$result);
                $orders = R::dispense('orders');
                $orders->seller_identifier = $seller_id;
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

       
    }
}
