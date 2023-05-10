<?php

namespace App\Console\Commands\Zoho;

use App\Services\Zoho\ZohoApi;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Services\Zoho\ZohoOrder;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_us;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\order\OrderItemDetails;
use App\Models\order\OrderUpdateDetail;

class ZohoCustomUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:custom:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zoho Custom Updater';

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
        // print "Remove Exit & Change the command & run the command";
        // exit;

        $zoho = new ZohoApi(new_zoho: false);
        // $zohoOrder = new ZohoOrder;

        $cnt = 0;

        $records = CSV_Reader('orderitemdetails.csv');

        $arr = [];
        $list_zeros = [];
        $lead_exists = [];

        foreach ($records as $record) {

            $amazon_order_id = $record["amazon_order_identifier"];
            $order_item_id = $record['order_item_identifier'];
            $asin = $record['asin'];
            $source = $record['source'];
            $store_name = $record['store_name'];
            $item_price = $record['item_price'];
            $item_tax = $record['item_tax'];

            $type_search = 'custom search';
            $exists = $zoho->search($amazon_order_id, $order_item_id, $type_search);

            if ($exists && array_key_exists('data', $exists) && array_key_exists(0, $exists['data']) && array_key_exists('id', $exists['data'][0])) {

                $amount_paid_by_customer = $this->amount_paid_by_customer($item_price, $item_tax);

                $price  = $this->prices($source, $asin, $amazon_order_id, $order_item_id, $store_name, $amount_paid_by_customer);

                if ($price != 0) {

                    $lead_exists[] = [
                        "amazon_order_identifier" => $amazon_order_id,
                        'order_item_identifier' => $order_item_id,
                        'asin' => $asin,
                        'source' => $source,
                        'store_name' => $store_name,
                        'item_price' => $item_price,
                        'item_tax' => $item_tax,
                    ];

                    $lead_id  = $exists['data'][0]['id'];
                    $parameters['Product_Cost']  = $price;
                    $type = 'zohocustom update';
                    $zoho->updateLead($lead_id, $parameters, $type);

                    po("Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Product Cost Updated: $price");
                    print("\n");
                } else {

                    $list_zeros[] = [
                        "amazon_order_identifier" => $amazon_order_id,
                        'order_item_identifier' => $order_item_id,
                        'asin' => $asin,
                        'source' => $source,
                        'store_name' => $store_name,
                        'item_price' => $item_price,
                        'item_tax' => $item_tax,
                    ];

                    po("Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Product Cost doesn't exists.");
                    print("\n");
                }

                // if ($orders) {

                //     $parameters = [];

                //     $parameters["US_EDD"] = Carbon::parse($orders->latest_delivery_date)->format('Y-m-d');

                //     $new_date = $parameters["US_EDD"];

                //     $zoho->updateLead($lead_id, $parameters);

                //     po("Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Old Date: $old_date & New Date: $new_date.");
                //     echo "\n";

                // } else {
                //     po("Ignore Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Did not find Shipping Address ");
                //     echo "<br>";
                //     exit;
                // }
            } else {
                po("Ignore Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Did not find in API");
                echo "<br>";
                exit;
            }
        }

        $headers = [
            "amazon_order_identifier",
            'order_item_identifier',
            'asin',
            'source',
            'store_name',
            'item_price',
            'item_tax',
        ];

        CSV_Write("list_zeros.csv", $headers, $list_zeros);
        CSV_Write("lead_exists.csv", $headers, $lead_exists);
    }

    public function prices($source, $asin, $order_identifier, $order_item_identifier, $store_name, $amount_paid_by_customer)
    {

        if ($source == "US") {

            $result = Catalog_us::where('asin', $asin)->limit(1)->first();
            $result_price = PricingUs::where('asin', $asin)->limit(1)->first();

            $price = $this->get_price_usa($asin, $order_identifier, $order_item_identifier, $result_price, $store_name, $amount_paid_by_customer);

            echo "\n";
            print($price);
            echo "\n";
        } else {
            $result = Catalog_in::where('asin', $asin)->limit(1)->first();
            $result_price = PricingIn::where('asin', $asin)->limit(1)->first();

            $price = $store_name == "Infinitikart UAE" ? 0 : $result_price->in_price;
        }

        return $price;
    }

    public function get_price_usa($asin, $order_identifier, $order_item_identifier, $result_price, $store_name, $amount_paid_by_customer)
    {


        if (!isset($result_price->us_price) && $store_name == "Infinitikart India") {

            return $amount_paid_by_customer * 0.012;
        }

        if ($store_name == "Infinitikart UAE") {

            return 0;
        }

        if (!isset($result_price->us_price)) {

            //slack Notification 
            // $slackMessage = 'US Price not found ' .
            //     'Amazon Order ID = ' . $amazon_order_identifier . ' ' .
            //     'Order Item Identifier = ' .  $amazon_order_item_identifier;
            // slack_notification('app360', 'Zoho Booking', $slackMessage);

            // insert to db (zoho_missin)
            // ZohoMissing::create([
            //     'asin' => $asin,
            //     'amazon_order_id' => $amazon_order_identifier,
            //     'order_item_id' => $amazon_order_item_identifier,
            //     'price' => '0',
            //     'status' => '0'
            // ]);

            return 0;
        }

        return $result_price->us_price;
    }

    public function amount_paid_by_customer($item_tax, $item_price): int
    {

        $item_price = json_decode($item_price);
        $item_tax = isset($item_tax) && !empty($item_tax) ? json_decode($item_tax) : 0;

        $item_tax                 = isset($item_tax->Amount) ? $item_tax->Amount  : 0;
        $amount_paid_by_customer  = isset($item_price->Amount) ? $item_price->Amount + $item_tax : 0;

        return (int)$amount_paid_by_customer;
    }
}
