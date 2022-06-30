<?php

namespace App\Services\SP_API\API\Order;

use Exception;
use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use App\Jobs\Seller\Seller_catalog_import_job;


class OrderItem
{
    use ConfigTrait;

    public function OrderItemDetails($order_id, $aws_id, $country_code)
    {

        $host = config('database.connections.order.host');
        $dbname = config('database.connections.order.database');
        $port = config('database.connections.order.port');
        $username = config('database.connections.order.username');
        $password = config('database.connections.order.password');

        if (!R::testConnection('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
            R::addDatabase('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            R::selectDatabase('order');
        }
        $config = $this->config($aws_id, $country_code);
        $marketplace_ids = $this->marketplace_id($country_code);
        $marketplace_ids = [$marketplace_ids];

        $apiInstance = new OrdersApi($config);
        $this->SelectedSellerOrderItem($apiInstance, $country_code, $order_id, $aws_id);
    }

    public function SelectedSellerOrderItem($apiInstance, $awsCountryCode, $order_id, $aws_id)
    {
        $data_element = array('buyerInfo');
        $next_token = NULL;

        try {

            $result_orderItems = $apiInstance->getOrderItems($order_id, $next_token, $data_element);
            $result_order_address = $apiInstance->getOrderAddress($order_id);

            $this->OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $awsCountryCode, $aws_id);
        } catch (Exception $e) {

            Log::warning($e->getMessage());
        }
        // sleep(45);
        // }
    }

    public function OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $awsCountryCode, $aws_id)
    {
        $order_address = '';
        $amazon_order = '';
        $data  = [];
        $result_order_address = (array)$result_order_address;
        foreach ($result_order_address as $result_address) {
            foreach ((array)$result_address['payload'] as $result) {
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
                        // $order_detials->$detailsKey = json_encode($value);
                        $order_address = json_encode($value);
                    } else {
                        $count = 1;
                        // $order_detials->$detailsKey = $value;
                        $amazon_order = $value;
                    }
                }
            }
        }
        $data = [];
        foreach ($result_orderItems['payload']['order_items'] as $result_order) {
            foreach ((array)$result_order as $result) {

                $order_detials = R::dispense('orderitemdetails');
                $order_detials->seller_identifier = $aws_id;
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
                    if ($detailsKey == 'asin') {
                        $asin = $value;
                    }
                }
                $order_detials->amazon_order_identifier = $amazon_order;
                $order_detials->shipping_address = $order_address;
                R::store($order_detials);

                //call catalog api
                // $check = DB::connection('catalog')->select("SELECT asin from catalog where asin = '$asin'");
                $check = [];
                //$type = 1 for seller, 2 for Order, 3 for inventory
                $data = NULL;
                if (count($check) <= 0) {

                    $data[] = [
                        'asin' => $asin,
                        'country_code' => $awsCountryCode,
                        'aws_id' => $aws_id,
                    ];

                    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
                        Seller_catalog_import_job::dispatch(
                            [
                                'datas' => $data,
                                'type' => 2,
                                'seller_id' => NULL
                            ]
                        )->onConnection('redis')->onQueue('catalog');
                    } else {

                        Seller_catalog_import_job::dispatch(
                            [
                                'datas' => $data,
                                'type' => 2,
                                'seller_id' => NULL
                            ]
                        );
                    }
                   
                }
            }
        }
        // DB::connection('order')
        //     ->update("UPDATE orders SET order_item = '1' where amazon_order_identifier = '$order_id'");
    }
}
