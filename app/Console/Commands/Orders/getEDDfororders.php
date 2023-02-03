<?php

namespace App\Console\Commands\Orders;

use Exception;
use App\Models\order\Order;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersV0Api;
use App\Services\SP_API\Config\ConfigTrait;

class getEDDfororders extends Command
{
    use ConfigTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:get_edd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Earliest Delivery date for Missing EDD from Order Table';

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
        // $headers = [
        //     'our_seller_identifier',
        //     'country',
        //     'amazon_order_identifier'
        // ];
        // // $order_statuses = ['Unshipped', 'PartiallyShipped', 'Shipped', 'InvoiceUnconfirmed', 'Canceled', 'Unfulfillable'];
        // $order_item_details = Order::query()
        //     ->select($headers)
        //     ->whereNull('earliest_delivery_date')
        //     ->limit(1)
        //     ->first();
        // $seller_id = ($order_item_details->our_seller_identifier);
        // $country_code = ($order_item_details->country);
        // $order_id = ($order_item_details->amazon_order_identifier);

        $ids = [
            '402-3959365-3730703',
            '403-4392820-9003520',
            '403-9642615-9375505',
            '404-6013041-5797947',
            '404-8182102-3934730',
            '405-9876397-3926751',
            '406-1903682-6651534',
            '406-2688181-0926713',
            '406-6778283-2263550',
            '406-9960068-6261942',
            '407-1286494-9881929',
            '407-8145503-7431545',
            '408-6012031-7807506'
        ];

        foreach ($ids as $data) {

            $seller_id = '35';
            $country_code = 'AE';
            $order_id = $data;
            Log::alert($data);

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

                $shipp_status = '';
                if (isset($result_data['orders']['0']['order_status'])) {
                    $shipp_status = ($result_data['orders']['0']['order_status']);
                } else {
                    Log::notice('(ship_status not Match for)' . $shipp_status);
                }

                $latest_delivery_date = null;
                if ($shipp_status = 'Shipped' || $shipp_status = 'Unshipped' || $shipp_status = 'PartiallyShipped') {
                    if (isset($result_data['orders']['0']['latest_delivery_date'])) {

                        $latest_delivery_date = ($result_data['orders']['0']['latest_delivery_date']);
                        Order::where('amazon_order_identifier', $order_id)->update(['latest_delivery_date' => $latest_delivery_date]);
                    }
                }
            } catch (Exception $e) {
                Log::alert('exception In mosh:get_edd command');
            }
        }
        po($latest_delivery_date);
    }
}
