<?php

namespace App\Services\SP_API\API\Order;

use Exception;
use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use App\Jobs\Orders\GetOrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\OrdersV0Api;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use App\Models\Admin\ErrorReporting;

class Order
{
    use ConfigTrait;
    private  $delay = 0;
    private $order_queue_name = '';
    public function SelectedSellerOrder($awsId, $awsCountryCode, $source, $awsAuth_code, $amazon_order_id)
    {
        $seller_id = $awsId;

        $host = config('database.connections.order.host');
        $dbname = config('database.connections.order.database');
        $port = config('database.connections.order.port');
        $username = config('database.connections.order.username');
        $password = config('database.connections.order.password');

        if (!R::testConnection('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
            R::addDatabase('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            R::selectDatabase('order');
        }

        $config = $this->config($awsId, $awsCountryCode, $awsAuth_code);
        $marketplace_ids = $this->marketplace_id($awsCountryCode);

        $marketplace_ids = [$marketplace_ids];

        $apiInstance = new OrdersV0Api($config);

        $subDays = getSystemSettingsValue('subDays', 5);
        $startTime = Carbon::now()->subDays($subDays)->toISOString();

        $createdAfter = $startTime;
        $max_results_per_page = 100;
        $next_token = NULL;

        $amazon_order_ids = $amazon_order_id ? [$amazon_order_id] : NULL;
        $order_statuses = [
            'Unshipped',
            'PartiallyShipped',
            'Shipped',
            'InvoiceUnconfirmed',
            'Canceled',
            'Unfulfillable'
        ];
        $order_statuses = NULL;
        try {
            next_token_exist:
            $results = $apiInstance->getOrders(
                $marketplace_ids,
                $createdAfter,
                $created_before = null,
                $last_updated_after = null,
                $last_updated_before = null,
                $order_statuses,
                $fulfillment_channels = null,
                $payment_methods = null,
                $buyer_email = null,
                $seller_order_id = null,
                $max_results_per_page,
                $easy_ship_shipment_statuses = null,
                $electronic_invoice_statuses = null,
                $next_token,
                $amazon_order_ids,
                $actual_fulfillment_supply_source_id = null,
                $is_ispu = null,
                $store_chain_store_id = null,
                $data_elements = null
            )->getPayload();
            $next_token = $results['next_token'];
            $this->OrderDataFormating($results, $awsCountryCode, $awsId, $source);

            if (isset($next_token)) {
                goto next_token_exist;
            }
            $orders = '';
            $amazon_order_id = '';
        } catch (Exception $e) {

            $code =  $e->getCode();
            $msg = $e->getMessage();

            ErrorReporting::create([
                'queue_type' => "order",
                'identifier' => $seller_id,
                'identifier_type' => "seller_id",
                'source' => $awsCountryCode,
                'aws_key' => $awsId,
                'error_code' => $code,
                'message' => $msg,
            ]);
        }
    }


    public function OrderDataFormating($results, $awsCountryCode, $awsId, $source)
    {
        $result_data = $results->getOrders();
        $result_data = json_decode(json_encode($result_data));
        $count = 0;

        foreach ($result_data as $resultkey => $result) {

            $orders = R::dispense('orders');
            $amazon_order_details = [];
            $orders->our_seller_identifier = $awsId;
            $orders->country = $awsCountryCode;
            $orders->source = $source;
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
                    } else {

                        $amazon_order_details[$detailsKey] = (string)$details;
                        $orders->{$detailsKey} = (string)$details;
                    }
                }
            }

            $data = DB::connection('order')
                ->select("SELECT id, amazon_order_identifier FROM orders 
            WHERE amazon_order_identifier = '$amazon_order_id'");

            if (array_key_exists(0, $data)) {

                $count++;
                $dataCheck = 1;
                $id = $data[0]->id;
                $update_orders = R::load('orders', $id);
                $update_orders->our_seller_identifier = $awsId;
                foreach ($amazon_order_details as $key => $value) {
                    $update_orders->{$key} = $value;
                }
                $update_orders->updated_at = now();

                R::store($update_orders);
            } else {

                $orders->order_item = '0';
                $orders->updated_at = now();
                $orders->created_at = now();

                R::store($orders);
            }
        }
    }
}
