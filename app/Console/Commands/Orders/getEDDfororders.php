<?php

namespace App\Console\Commands\Orders;

use Exception;
use App\Models\order\Order;
use App\Models\order\OrderItemDetails;
use App\Services\Zoho\ZohoApi;
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
    protected $signature = 'mosh:get_edd {orderids} {store_id}';

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
        $order_ids = $this->argument('orderids');
        $country = $this->argument('store_id');

        $order_idarray = explode(',', $order_ids);
        $source_data  = explode('_', $country);

        $seller_id = $source_data['0'];
        $country_code = $source_data['1'];

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

                        $item_ids =  OrderItemDetails::query()
                            ->select('order_item_identifier', 'asin')
                            ->where('amazon_order_identifier', $order_id)
                            ->get();

                        foreach ($item_ids as $data) {

                            $item_id =   $data['order_item_identifier'];
                            $zoho = new ZohoApi(new_zoho: false);
                            $type = 'EDD Update Through Command';
                            $zoho_lead_search = $zoho->search($order_id, $item_id,$type);

                            if (isset($zoho_lead_search['data'][0]['id'])) {
                                $lead_id = $zoho_lead_search['data']['0']['id'];
                                $value = Carbon::parse($latest_delivery_date)->format('Y-m-d');
                                $type = 'EDD Update Through Command';
                                $zoh =    $zoho->updateLead($lead_id, ["US_EDD" => $value], $type);

                                Order::where('amazon_order_identifier', $order_id)->update(['latest_delivery_date' => $latest_delivery_date]);
                            } else {
                                Log::info("Not A valid Key For EDD(zoho) " . ' ' . $order_id);
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                Log::alert('exception In mosh:get_edd command');
            }
        }
    }
}
