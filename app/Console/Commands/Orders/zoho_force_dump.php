<?php

namespace App\Console\Commands\Orders;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersV0Api;
use App\Models\order\Order as OrderModel;
use App\Services\SP_API\Config\ConfigTrait;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class zoho_force_dump extends Command
{
    use ConfigTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho_force_dump {orderids} {store_id}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take Order_id as Input And dump to zoho';

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

        $process_manage = [
            'module'             => 'Orders',
            'description'        => 'Dump Missing Orders',
            'command_name'       => 'mosh:zoho_force_dump',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];
        // $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end

        $order_ids = $this->argument('orderids');
        $country = $this->argument('store_id');

        $order_idarray = explode(',', $order_ids);
        $source_data  = explode('_', $country);

        $seller_id = $source_data['0'];
        $country_code = $source_data['1'];

        foreach ($order_idarray as $order_id) {

            $token = NULL;
            $config = $this->config($seller_id, $country_code, $token);
            $marketplace_ids = $this->marketplace_id($country_code);
            $marketplace_ids = [$marketplace_ids];

            $apiInstance = new OrdersV0Api($config);
            $startTime = Carbon::now()->subDays(2)->toISOString();
            $createdAfter = $startTime;
            $max_results_per_page = 100;

            $order_statuses = null;
            $next_token = NULL;
            $amazon_order_ids = [$order_id];

            try {

                $order = $apiInstance->getOrders($marketplace_ids, $createdAfter, $created_before = null, $last_updated_after = null, $last_updated_before = null, $order_statuses, $fulfillment_channels = null, $payment_methods = null, $buyer_email = null, $seller_order_id = null, $max_results_per_page, $easy_ship_shipment_statuses = null, null, $next_token, $amazon_order_ids, $actual_fulfillment_supply_source_id = null, $is_ispu = null, $store_chain_store_id = null, $data_elements = null);
                $request_id = $order['headers']['x-amzn-RequestId'];
                $result_data =  $order->getPayload();

                $this->OrderDataFormating($result_data, $country_code, $seller_id, 'US');
            } catch (Exception $e) {
                Log::alert('Order Details Not Found for '. $order_id .'-' . 'store_id - ' . $seller_id );
            }
        }

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }

    public function OrderDataFormating($results, $awsCountryCode, $awsId, $source)
    {
        $fillable_columns = [
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
        $result_data = $results;
        $result_data = json_decode(json_encode($result_data));

        foreach ($result_data as $resultkey => $result) {

            $amazon_order_details = [];


            $amazon_order_details['our_seller_identifier'] = $awsId;
            $amazon_order_details['country'] = $awsCountryCode;
            $amazon_order_details['source'] = $source;
            $amazon_order_details['order_item'] = 0;

            foreach ($result['0'] as $detailsKey => $details) {



                $detailsKey = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $detailsKey));
                $detailsKey = lcfirst($detailsKey);
                $id = substr($detailsKey, -2);

                if ($id == 'Id' || $id == 'id') {
                    $detailsKey = str_replace(["Id", "id"], "identifier", $detailsKey);
                }

                $detailsKey = str_replace('is_is_pu', 'is_ispu', $detailsKey);

                if (array_search($detailsKey, $fillable_columns)) {

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
        }
    }
}
