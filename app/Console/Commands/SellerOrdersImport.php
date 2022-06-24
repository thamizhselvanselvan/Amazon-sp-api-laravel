<?php

namespace App\Console\Commands;

use RedBeanPHP\R;
use Aws\AwsClient;
use Carbon\Carbon;
use App\Models\Aws_credential;
use Illuminate\Console\Command;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Artisan;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use AmazonPHP\SellingPartner\Exception\Exception;

class SellerOrdersImport extends Command
{
    use ConfigTrait;
    public $seller_id;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:sellers-orders-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Seller orders from Amazon for selected seller';

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
        // Log::info('seller order import working every 30 mins.');
        $host = config('database.connections.order.host');
        $dbname = config('database.connections.order.database');
        $port = config('database.connections.order.port');
        $username = config('database.connections.order.username');
        $password = config('database.connections.order.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        $aws_data = OrderSellerCredentials::where('dump_order', 1)->get();

        foreach ($aws_data as $aws_value) {

            $awsId  = $aws_value['id'];
            $awsCountryCode = $aws_value['country_code'];
            $this->seller_id = $aws_value['seller_id'];
            $bb_aws_cred = Aws_credential::where('seller_id', $this->seller_id)->get();
            $awsAuth_code = $bb_aws_cred[0]->auth_code;          
            $this->SelectedSellerOrder($awsId, $awsCountryCode, $awsAuth_code);
            
        }
      
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            Log::warning("Export asin command executed local !");
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:seller-order-item-import > /dev/null &";
            exec($command);
        } else {

            Artisan::call('pms:seller-order-item-import ');
        }
    }

    public function SelectedSellerOrder($awsId, $awsCountryCode, $awsAuth_code)
    {

        $config = $this->config($awsId, $awsCountryCode, $awsAuth_code);
        $marketplace_ids = $this->marketplace_id($awsCountryCode);
        $marketplace_ids = [$marketplace_ids];

        $apiInstance = new OrdersApi($config);
        // $startTime = Carbon::now()->subMinute(20)->toISOString();
        // $startTime = Carbon::now()->subHours(4)->toISOString();
        $startTime = Carbon::now()->subDays(25)->toISOString();

        // echo $startTime;
        $createdAfter = $startTime;
        $lastUpdatedBefore = now()->toISOString();
        $max_results_per_page = 100;
        $next_token = NULL;

        try {

            next_token_exist:
            $results = $apiInstance->getOrders($marketplace_ids, $createdAfter, $created_before = null, $last_updated_after = null, $last_updated_before = null, $order_statuses = null, $fulfillment_channels = null, $payment_methods = null, $buyer_email = null, $seller_order_id = null, $max_results_per_page, $easy_ship_shipment_statuses = null, $next_token, $amazon_order_ids = null, $actual_fulfillment_supply_source_id = null, $is_ispu = null, $store_chain_store_id = null, $data_elements = null)->getPayload();
            $next_token = $results['next_token'];
            $this->OrderDataFormating($results, $awsCountryCode);

            if (isset($next_token)) {
                goto next_token_exist;
            }
            $orders = '';
            $amazon_order_id = '';
        } catch (Exception $e) {

            Log::warning('Exception when calling OrdersApi->getOrders: ', $e->getMessage(), PHP_EOL);
        }
    }

    public function UpdateOrderStatus()
    {

        
    }

    public function OrderDataFormating($results, $awsCountryCode)
    {
        $result_data = $results->getOrders();
        $result_data = json_decode(json_encode($result_data));
        $count = 0;
        foreach ($result_data as $resultkey => $result) {

            $orders = R::dispense('orders');
            $amazon_order_details = [];
            $orders->our_seller_identifier = $this->seller_id;
            $orders->country = $awsCountryCode;
            $amazon_order_id = '';
            foreach ((array)$result as $detailsKey => $details) {


                $detailsKey = lcfirst($detailsKey);
                if (is_Object($details)) {

                    $amazon_order_details[$detailsKey] = json_encode($details);
                    $orders->{$detailsKey} = json_encode($details);
                } else if (is_array($details)) {

                    $amazon_order_details[$detailsKey] = json_encode($details);
                    $orders->{$detailsKey} = json_encode($details);
                } else {

                    $id = substr($detailsKey, -2);
                    if ($id == 'Id') {
                        $detailsKey = str_replace("Id", "Identifier", $detailsKey);
                    }
                    if ($detailsKey == 'amazonOrderIdentifier') {

                        $amazon_order_id = $details;
                        $amazon_order_details['amazon_order_identifier'] = $details;
                        $orders->amazon_order_identifier = $details;
                    }
                   
                    else {

                        $amazon_order_details[$detailsKey] = (string)$details;
                        $orders->{$detailsKey} = (string)$details;
                    }
                }
            }
            
            //$amazon_order_id = '407-0297568-739477566';

            $data = DB::connection('order')->select("select id, amazon_order_identifier from orders where amazon_order_identifier = '$amazon_order_id'");
        //   $data = [];
            if (array_key_exists(0, $data)) 
            {
                $count++;
                $dataCheck = 1;
                $id = $data[0]->id;
                $update_orders = R::load('orders', $id);
                $update_orders->our_seller_identifier = $this->seller_id;
                foreach ($amazon_order_details as $key => $value) {
                    $update_orders->{$key} = $value;
                }
                $update_orders->updatedat = now();
                R::store($update_orders);

            } else {

                $orders->order_item = '0';
                $orders->updatedat = now();
                $orders->createdat = now();
                // dd($orders);
                R::store($orders);
            }
        }
        return true;
    }
}
