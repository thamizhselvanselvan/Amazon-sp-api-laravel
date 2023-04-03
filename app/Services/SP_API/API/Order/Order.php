<?php

namespace App\Services\SP_API\API\Order;

use Exception;
use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\order\Order as OrderModel;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use App\Jobs\Orders\GetOrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ErrorReporting;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\OrdersV0Api;
use App\Models\order\Order as OrderOrder;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class Order
{
    use ConfigTrait;
    private  $delay = 0;
    private $order_queue_name = '';
    private $store_name;
    private  $fillable_columns = [
        'our_seller_identifier',
        'country',
        'source',
        'amazon_order_identifier',
        'purchase_date',
        'last_update_date',
        'order_status',
        'fulfillment_channel',
        'sales_channel',
        'ship_service_level',
        'order_total',
        'number_of_items_shipped',
        'number_of_items_unshipped',
        'payment_method',
        'payment_method_details',
        'marketplace_identifier',
        'shipment_service_level_category',
        'order_type',
        'earliest_ship_date',
        'latest_ship_date',
        'earliest_delivery_date',
        'latest_delivery_date',
        'is_business_order',
        'is_prime',
        'is_premium_order',
        'is_global_express_enabled',
        'is_replacement_order',
        'is_sold_by_ab',
        'default_ship_from_location_address',
        'is_ispu',
        'shipping_address',
        'buyer_info',
        'automated_shipping_settings',
        'order_item',
        'seller_order_identifier',
        'is_access_point_order',
        'has_regulated_items',
        'easy_ship_shipment_status',
        'payment_execution_detail',
        'replaced_order_identifier'
    ];

    public function SelectedSellerOrder($awsId, $awsCountryCode, $source, $awsAuth_code, $amazon_order_id, $store_name)
    {
        $seller_id = $awsId;
        $this->store_name = $store_name;

        $config = $this->config($awsId, $awsCountryCode, $awsAuth_code);
        $marketplace_ids = $this->marketplace_id($awsCountryCode);

        $marketplace_ids = [$marketplace_ids];

        $apiInstance = new OrdersV0Api($config);

        $subDays = getSystemSettingsValue('dump_order_subDays', 5);
        $startTime = Carbon::now()->subDays($subDays)->toISOString();

        $createdAfter = $startTime;
        $max_results_per_page = 100;
        $next_token = NULL;

        $amazon_order_ids = $amazon_order_id ? $amazon_order_id : NULL;
        $amazon_order_ids = is_array($amazon_order_ids) ? $amazon_order_id : [$amazon_order_ids];

        try {
            next_token_exist:
            $results = $apiInstance->getOrders(
                $marketplace_ids,
                $createdAfter,
                $created_before = null,
                $last_updated_after = null,
                $last_updated_before = null,
                $order_statuses = null,
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

            if ($code == '403') {
                $s_name = $this->store_name;
                (new CheckStoreCredServices())->updateTable($awsId, '0');
                $slackMessage = "Code: 403
                Store Name: $s_name
                Country: $awsCountryCode
                Description: Store credential is not Working";
                // Log::alert($slackMessage);
                slack_notification('app360', 'Store Credential Check', $slackMessage);
            }
        }
    }

    public function OrderDataFormating($results, $awsCountryCode, $awsId, $source)
    {
        $result_data = $results->getOrders();
        $result_data = json_decode(json_encode($result_data));

        foreach ($result_data as $resultkey => $result) {

            $amazon_order_details = [];
            $missing_clmn = '';
            $amazon_order_id = '';

            $amazon_order_details['our_seller_identifier'] = $awsId;
            $amazon_order_details['country'] = $awsCountryCode;
            $amazon_order_details['source'] = $source;
            $amazon_order_details['order_item'] = 0;

            foreach ($result as $detailsKey => $details) {

                $detailsKey = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $detailsKey));
                $detailsKey = lcfirst($detailsKey);
                $id = substr($detailsKey, -2);
                if ($id == 'Id' || $id == 'id') {
                    $detailsKey = str_replace(["Id", "id"], "identifier", $detailsKey);
                }

                $detailsKey = str_replace('is_is_pu', 'is_ispu', $detailsKey);

                if (array_search($detailsKey, $this->fillable_columns)) {

                    if (is_Object($details) || is_array($details)) {
                        $amazon_order_details[$detailsKey] = json_encode($details);
                    } else {
                        if ($detailsKey == 'amazon_order_identifier') {
                            $amazon_order_id = $details;
                            $amazon_order_details[$detailsKey] = $details;
                        } else {
                            $amazon_order_details[$detailsKey] = $details;
                        }
                    }
                } else {
                    $missing_clmn .= $detailsKey . ', ';
                }
            }
            if ($amazon_order_details['order_status'] == 'Shipped' || $amazon_order_details['order_status'] == 'Unshipped' || $amazon_order_details['order_status'] == 'PartiallyShipped') {

                OrderModel::upsert(
                    $amazon_order_details,
                    ['amazon_order_identifier_UNIQUE'],
                    [
                        'id',
                        'buyer_info',
                        'last_update_date',
                        'order_status',
                        'number_of_items_shipped',
                        'number_of_items_unshipped',
                    ]
                );
            }

            $this->notifyMissingClmns($missing_clmn, $awsId, $amazon_order_id);
        }
    }

    public function notifyMissingClmns($missing_columns_txt, $store_id, $order_id)
    {
        if ($missing_columns_txt) {

            $store_name = $this->store_name;
            $column_name = substr($missing_columns_txt, 0, -2);
            $slackMessage = "Message: New Columns Detected
            Store Id: $store_id
            Store Name: $store_name
            Order Id: $order_id
            New Columns: $column_name";

            // Log::alert($slackMessage);
            slack_notification('app360', 'Order Import', $slackMessage);
        }
        return true;
    }
}
