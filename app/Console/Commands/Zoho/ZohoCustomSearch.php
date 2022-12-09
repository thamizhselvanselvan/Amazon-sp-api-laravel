<?php

namespace App\Console\Commands\Zoho;

use App\Services\Zoho\ZohoApi;
use Illuminate\Console\Command;
use App\Services\Zoho\ZohoOrder;
use App\Models\order\OrderItemDetails;

class ZohoCustomSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho:search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zoho Search';

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
        $zoho = new ZohoApi;
        $zohoOrder = new ZohoOrder;

        $cnt = 0;

        $records = CSV_Reader("Zoho Id's Not Dump.csv");

        foreach ($records as $record) {

            $date = $record['Date'];
            $amazon_order_id = $record['order-id'];
            $order_item_id = $record['order-item-id'];
            $store = $record['Store'];

            $exists = $zoho->search($amazon_order_id, $order_item_id);

            $order_details = [
                "date" => $date,
                "amazon_order_id" => $amazon_order_id,
                "order_item_id" => $order_item_id,
                "store" => $store,
                "Zoho Leade id" => '',
            ];

            $headers = [
                "Date",
                "order-id",
                "order-item-id",
                "Store",
                "zoho-lead-id",
            ];

            if ($exists && array_key_exists('data', $exists) && array_key_exists(0, $exists['data']) && array_key_exists('id', $exists['data'][0])) {

                $lead_id = $exists['data'][0]['id'];
                $lead_source = $exists['data'][0]['Lead_Source'];
                $zip_code = $exists['data'][0]['Zip_Code'];

                $order_details['Zoho Leade id'] = $lead_id;


                CSV_w("All Zoho ID CHECK.CSV", [$order_details], $headers);
            } else {

                CSV_w("All Zoho ID CHECK.CSV", [$order_details], $headers);

                print ("Ignore Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Did not find in API") . PHP_EOL;
                //echo "<br>";
            }
        }
    }
}
