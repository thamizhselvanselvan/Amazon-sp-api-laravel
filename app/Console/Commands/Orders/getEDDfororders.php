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

            '402-3319504-5606740',
            '408-8302463-4382711',
            '407-8412506-0476352',
            '403-6218831-7733922',
            '408-5905702-8903569',
            '171-2634966-4823562',
            '402-2485555-2088360',
            '402-2625634-9970755',
            '402-2332124-7792301',
            '402-5893494-0778759',
            '402-4489995-9984331',
            '403-7654066-7933131',
            '405-8766145-2363531',
            '403-8079198-2512358',
            '408-8465228-1030701',
            '404-1005504-0335524',
            '408-5368961-2253126',
            '406-6116435-1040322',
            '402-0487385-2393134',
            '402-0310060-8931556',
            '171-9250536-2637940',
            '404-7863839-2185925',
            '404-9278767-9162707',
            '171-3110485-6781112',
            '408-4240124-6709945',
            '405-3490856-9422744',
            '402-6821137-0665903',
            '404-5778064-9668309',
            '402-0102055-7873143',
            '405-1558399-3369910',
            '171-5089168-2093960',
            '171-4558146-5205935',
            '408-9829002-3513140',
            '406-7711342-8204356',
            '404-5289216-9617108',
            '404-7714726-3877130',
            '171-0028040-9432314',
            '405-8244508-5010720',
            '407-2343742-1233109',
            '403-7293567-2798702',
            '404-9644828-3229927',
            '402-1916500-4041152',
            '408-5095531-2813941',
            '406-1009815-1741128',
            '403-2262115-8050707'    
           
        ];

        foreach ($ids as $data) {

            $seller_id = '29';
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
