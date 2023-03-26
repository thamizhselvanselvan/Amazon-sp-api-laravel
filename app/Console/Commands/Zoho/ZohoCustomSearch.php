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
        $zoho = new ZohoApi(new_zoho: false);
        $zohoOrder = new ZohoOrder;

        $cnt = 0;

        $records = CSV_Reader("order_update_details_all.csv");

        foreach ($records as $record) {

            $date = $record['Date'] ?? '';
            $amazon_order_id = trim($record['amazon_order_id']);
            $order_item_id = trim($record['order_item_id']);
            $zoho_status = $record['zoho_status'] ?? 1;

            $exists = $zoho->search($amazon_order_id, $order_item_id);

            $order_details = [
                "date" => $date,
                "amazon_order_id" => $amazon_order_id,
                "order_item_id" => $order_item_id,
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
                $amount = $exists['data'][0]['Amount_Paid_by_Customer'];

                $order_details['Zoho Leade id'] = $lead_id;

                $order_table_name = 'orders';
                $order_item_table_name = 'orderitemdetails';

                $order_details = [
                    "$order_item_table_name.seller_identifier",
                    "$order_item_table_name.asin",
                    "$order_item_table_name.seller_sku",
                    "$order_item_table_name.title",
                    "$order_item_table_name.order_item_identifier",
                    "$order_item_table_name.quantity_ordered",
                    "$order_item_table_name.item_price",
                    "$order_item_table_name.item_tax",
                    "$order_item_table_name.shipping_address",

                    "$order_table_name.fulfillment_channel",
                    "$order_table_name.our_seller_identifier",
                    "$order_table_name.amazon_order_identifier",
                    "$order_table_name.purchase_date",
                    "$order_table_name.earliest_delivery_date",
                    "$order_table_name.buyer_info",
                    "$order_table_name.order_total",
                    "$order_table_name.latest_delivery_date",
                    "$order_table_name.is_business_order",
                ];



                $order_item_details = OrderItemDetails::select($order_details)
                    ->join('orders', 'orderitemdetails.amazon_order_identifier', '=', 'orders.amazon_order_identifier')
                    ->where('orderitemdetails.amazon_order_identifier', $amazon_order_id)
                    ->where('orderitemdetails.order_item_identifier', $order_item_id)
                    ->with(['store_details.mws_region'])
                    ->limit(1)
                    ->first();

                $item_price                              = json_decode($order_item_details->item_price);
                $item_tax                                = isset($order_item_details->item_tax) && !empty($order_item_details->item_tax) ? json_decode($order_item_details->item_tax) : 0;
                $item_tax                                = isset($item_tax->Amount) ? $item_tax->Amount  : 0;
                $amount_paid_by_customer                 = isset($item_price->Amount) ? $item_price->Amount + $item_tax : 0;
                $prod_array["Amount_Paid_by_Customer"]   = (int)$amount_paid_by_customer;


                if (isset($item_price->Amount)) {
                    $gn = $item_price->Amount . " Item Price & Tax Price " . $item_tax;

                    if ($item_tax == "0.00" || $item_tax == 0) {
                        print ($amazon_order_id . " & " . $order_item_id  . " Already Price Updated " . $amount_paid_by_customer) . PHP_EOL;
                    } else {
                        $zoho->updateLead($lead_id, $prod_array);
                        print ($amazon_order_id . " & " . $order_item_id . " " . $gn . " Updated with Prices " . $amount_paid_by_customer . " Previous Pices " . $amount . " Zoho Status " . $zoho_status) . PHP_EOL;
                    }
                }


                //CSV_w("All Zoho ID CHECK.CSV", [$order_details], $headers);
            } else {

                //("All Zoho ID CHECK.CSV", [$order_details], $headers);

                print ("Ignore Amazon Order ID: $amazon_order_id Order Item ID: $order_item_id. Did not find in API") . PHP_EOL;
                //echo "<br>";
            }
        }
    }
}
