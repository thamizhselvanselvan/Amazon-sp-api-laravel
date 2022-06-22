<?php

namespace App\Console\Commands;

use Exception;
use RedBeanPHP\R;
use App\Models\Aws_credential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Catalog;
use SellingPartnerApi\Api\OrdersApi;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use App\Jobs\Seller\Seller_catalog_import_job;

class SellerOrdersItemImport extends Command
{
    use ConfigTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:seller-order-item-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import seller order item import from amazon sp api';

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
        $host = config('database.connections.order.host');
        $dbname = config('database.connections.order.database');
        $port = config('database.connections.order.port');
        $username = config('database.connections.order.username');
        $password = config('database.connections.order.password');
        
        R::addDatabase('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        R::selectDatabase('order');
        // R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        $aws_data = OrderSellerCredentials::where('get_order_item', 1)->get();
        foreach ($aws_data as $aws_value) {

            $awsId  = $aws_value['id'];
            $awsCountryCode = $aws_value['country_code'];
            $seller_id = $aws_value['seller_id'];
            $bb_aws_cred = Aws_credential::where('seller_id', $seller_id)->get();
            $awsAuth_code = $bb_aws_cred[0]->auth_code;

            $config = $this->config($awsId, $awsCountryCode, $awsAuth_code);
            $marketplace_ids = $this->marketplace_id($awsCountryCode);
            $marketplace_ids = [$marketplace_ids];

            $apiInstance = new OrdersApi($config);
            $this->SelectedSellerOrderItem($apiInstance, $seller_id, $awsCountryCode);
        }
        Log::info('order item saved');
        // exit;
        R::close();

        //After importing order item detials of particult order id, get detials of asin if asin is not avaliable in mosh_catalog.catlaog
        $order_item_details = DB::connection('order')->select("SELECT seller_identifier, asin, country from orderitemdetails where status = 0 ");
        $count = 0;
        $batch = 0;
        $asinList = [];
        foreach ($order_item_details as $key => $value) {
            $asin = $value->asin;
            // $check = DB::connection('catalog')->select("SELECT asin from catalog where asin = '$asin'");
            $check = [];
            if (!array_key_exists('0', $check)) {
                // $asinList[$count]->asin = $asin;
                $count++;
                $batch++;
                $data[] = $value;
            }
            
            //$type = 1 for seller, 2 for Order, 3 for inventory
            if ($count == 10) {
                $count = 0;
                $type = 2;
                $catalog = new Catalog();
                $catalog->index($data, $seller_id, $type, $batch);
                Log::alert('10 asin imported');
            }
        }

        $data = [];
        $seller_id = [];
       
    }

    public function SelectedSellerOrderItem($apiInstance, $seller_id, $awsCountryCode)
    {
        $amazonorder_ids = DB::connection('order')->select("SELECT amazon_order_identifier from orders where our_seller_identifier = $seller_id AND order_item = 0");
        
        foreach ($amazonorder_ids as $amazonorder_id) {
            $order_id = ($amazonorder_id->amazon_order_identifier);
            $data_element = array('buyerInfo');
            $next_token = NULL;

            try {

                $result_orderItems = $apiInstance->getOrderItems($order_id, $next_token, $data_element);
                $result_order_address = $apiInstance->getOrderAddress($order_id);

                $this->OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $seller_id, $awsCountryCode);
            } catch (Exception $e) {

                Log::warning($e->getMessage());
            }
            sleep(30);
        }
    }

    public function OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $seller_id, $awsCountryCode)
    {
        foreach ($result_orderItems['payload']['order_items'] as $result_order) {
            foreach ((array)$result_order as $result) {
                $order_detials = R::dispense('orderitemdetails');
                $order_detials->seller_identifier = $seller_id;
                $order_detials->status = '0';
                $order_detials->country = $awsCountryCode;

                foreach ($result as $key => $value) {
                    $detailsKey = lcfirst($key);
                    $id = substr($detailsKey, -2);
                    $ids = substr($detailsKey, -3);
                    // echo $id;
                    if ($id == 'id' || $id == 'Id' || $ids == 'ids') {
                        $detailsKey = str_replace(["id", 'Id', 'ids'], "identifier", $detailsKey);
                    }

                    if (is_array($value)) {

                        $order_detials->{$detailsKey} = json_encode($value);
                    } elseif (is_object(($value))) {
                        $order_detials->{$detailsKey} = json_encode($value);
                    } else {
                        $order_detials->{$detailsKey} = ($value);
                    }
                }
                R::store($order_detials);
            }
        }
        $result_order_address = (array)$result_order_address;
        foreach ($result_order_address as $result_address) {
            foreach ((array)$result_address['payload'] as $result) {
                // $order_detials = NULL;
                // $order_detial = R::dispense('orderitemdetails');
                $count = 0;
                foreach ($result as $key => $value) {

                    $detailsKey = lcfirst($key);
                    $id = substr($detailsKey, -2);
                    $ids = substr($detailsKey, -3);
                    // echo $id;
                    if ($id == 'id' || $id == 'Id' || $ids == 'ids') {
                        $detailsKey = str_replace(["id", 'Id', 'ids'], "identifier", $detailsKey);
                    }

                    if (is_array($value) || is_object($value)) {
                        $order_detials->$detailsKey = json_encode($value);
                    } else {
                        $count = 1;
                        $order_detials->$detailsKey = $value;
                    }
                }
                R::store($order_detials);
            }
        }

        DB::connection('order')
            ->update("UPDATE orders SET order_item = '1' where amazon_order_identifier = '$order_id'");
    }
}
