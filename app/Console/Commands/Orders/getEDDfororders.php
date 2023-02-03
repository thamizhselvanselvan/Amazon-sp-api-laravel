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
            '408-7083897-3501144',
            '405-3003832-1389157',
            '403-5765407-9282706',
            '402-8412411-3052319',
            '407-0071546-7614768',
            '403-8183882-6610758',
            '408-7296982-4128340',
            '404-9399685-8550717',
            '403-4936337-0796317',
            '408-6363424-6939502',
            '407-4819458-9651513',
            '405-2112671-8684347',
            '408-3395804-7383566',
            '403-6072552-9509904',
            '405-5958950-7268302',
            '171-8092063-1346746',
            '407-8312327-4253928',
            '406-1361807-7917145',
            '403-4208236-5233918',
            '405-3543716-6648365',
            '404-7440193-6081124',
            '404-7440193-6081124',
            '404-7440193-6081124',
            '408-7285207-3741964',
            '408-9783351-0885923',
            '407-8145503-7431545',
            '406-2688181-0926713',
            '406-6778283-2263550',
            '404-9131496-4967547',
            '404-6013041-5797947',
            '171-3953411-5481949',
            '408-4504451-8704369',
            '404-8182102-3934730',
            '408-8499744-7472357',
            '408-3762509-7949143',
            '171-4778597-1252317',
            '408-8588800-8239558',
            '402-0118709-2438748',
            '402-0118709-2438748',
            '402-3959365-3730703',
            '405-5397692-3500320',
            '403-1946580-8121138',
            '403-7778527-6489921',
            '407-1286494-9881929',
            '404-8085776-5683542',
            '405-5217689-1044339',
            '405-5217689-1044339',
            '408-8967983-8192367',
            '405-5288756-6636356',
            '171-6585730-2997963',
            '171-6585730-2997963',
            '403-6478256-8486736',
            '171-4245070-9685925',
            '402-9413943-4956357',
            '403-6532923-1947548',
            '408-1911803-8816353',
            '403-9072440-7276356',
            '406-0238976-6772325',
            '171-2066948-9508334',
            '403-9221906-1190765',
            '402-1213532-6213134',
            '406-3524416-7278710',
            '404-4825888-2057948',
            '404-4825888-2057948',
            '403-4538332-4210757',
            '406-9954534-3284314',
            '402-8577162-8108328',
            '406-2212757-6580320',
            '404-1456698-2677901',
            '402-4084645-6818728',
            '408-7324559-3418756',
            '405-0934457-7423523',
            '406-4185019-3432313',
            '406-1903682-6651534',
            '406-0442787-8641907',
            '406-9465127-1329136',
            '402-0428020-1907532',
            '405-4868555-8833954',
            '408-6012031-7807506',
            '171-7735384-7101966',
            '403-7167695-4910725',
            '171-9983908-1482737',
            '405-9876397-3926751',
            '405-0571419-3271564',
            '404-2396070-7217965',
            '403-9078408-5733138',
            '403-9078408-5733138',
            '405-2223815-1310750',
            '404-2344345-0192317',
            '403-4392820-9003520',
            '404-1991812-5449130',
            '405-3334810-8699536',
            '171-0600305-2046728',
            '403-9642615-9375505',
            '171-1059024-2525128',
            '406-9960068-6261942',
            '403-2427810-0237127',
            '405-4736316-8207552',
            '403-3354569-3776304',
            '405-4736316-8207552',
            '403-7250702-6893149',
            '403-7250702-6893149',
            '402-7943428-0236314',
            '406-3165769-3098701',
            '406-5067215-6405908',
            '403-6329409-4009168',
            '406-7453230-0284312',
            '405-8264219-9900309'
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
