<?php

namespace App\Console\Commands\Zoho;

use App\Services\Zoho\ZohoApi;
use Illuminate\Console\Command;
use App\Services\Zoho\ZohoOrder;
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
        print "Remove Exit & Change the command & run the command";
        exit;

        $zoho = new ZohoApi;
        $zohoOrder = new ZohoOrder;

        $cnt = 0;

        $records = CSV_Reader('ord_order_update_details (2).csv');

        foreach ($records as $record) {

            $amazon_order_id = $record['amazon_order_id'];
            $order_item_id = $record['order_item_id'];

            $exists = $zoho->search($amazon_order_id, $order_item_id);

            if ($exists && array_key_exists('data', $exists) && array_key_exists(0, $exists['data']) && array_key_exists('id', $exists['data'][0])) {

                $lead_id = $exists['data'][0]['id'];
                $lead_source = $exists['data'][0]['Lead_Source'];
                $zip_code = $exists['data'][0]['Zip_Code'];

                $shipping_address = OrderItemDetails::query()
                    ->select('shipping_address', 'seller_identifier')
                    ->where("amazon_order_identifier", $amazon_order_id)
                    ->where("order_item_identifier", $order_item_id)
                    ->with(['store_details.mws_region'])
                    ->first();

                if ($shipping_address) {

                    $country_code = $zohoOrder->get_country_code($shipping_address->store_details);

                    if (!empty($country_code)) {

                        $parameters = [];

                        $parameters["Address"] = $zohoOrder->get_address($shipping_address->shipping_address, $country_code);

                        if (!$zip_code) {
                            $parameters["Zip_Code"] = $zohoOrder->get_state_pincode($country_code, (object)$shipping_address->shipping_address, 'pincode');
                        }

                        echo "$lead_source -- $lead_id :- " . $parameters["Address"] . " ZIP CODE MIZZ ->"  . " <br>";

                        if (isset($parameters['Zip_Code'])) {
                            echo $parameters['Zip_Code'] . "<br>";
                        }

                        if ($cnt == 5) {
                            $cnt = 0;
                        }

                        $cnt++;
                        $zoho->updateLead($lead_id, $parameters);
                    } else {
                        po("Ignore Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Did not find Country Code ");
                        echo "<br>";
                        exit;
                    }
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
