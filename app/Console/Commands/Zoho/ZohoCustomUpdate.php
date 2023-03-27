<?php

namespace App\Console\Commands\Zoho;

use App\Services\Zoho\ZohoApi;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Services\Zoho\ZohoOrder;
use Illuminate\Support\Facades\DB;
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

        $records = CSV_Reader('Order ids to update.csv');

        $arr = [];
        foreach ($records as $record) {

            $amazon_order_id = $record["Amazon Order ID"];
            $order_item_id = $record['Order Item Details'];
            

            $exists = $zoho->search($amazon_order_id, $order_item_id);

            if ($exists && array_key_exists('data', $exists) && array_key_exists(0, $exists['data']) && array_key_exists('id', $exists['data'][0])) {

                $lead_id  = $exists['data'][0]['id'];
                $old_date = $exists['data'][0]['US_EDD'];

                $orders = DB::connection("order")->table("orders")->where('amazon_order_identifier', $amazon_order_id)->first();

                if ($orders) {

                    $parameters = [];

                    $parameters["US_EDD"] = Carbon::parse($orders->latest_delivery_date)->format('Y-m-d');

                    $new_date = $parameters["US_EDD"];

                    $zoho->updateLead($lead_id, $parameters);

                    po("Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Old Date: $old_date & New Date: $new_date.");
                    echo "\n";

                } else {
                    po("Ignore Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Did not find Shipping Address ");
                    echo "<br>";
                    exit;
                }
            } else {
                po("Ignore Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Did not find in API");
                echo "<br>";
                exit;
            }
        }
    }
}
