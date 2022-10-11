<?php

namespace App\Console\Commands\Orders;

use League\Csv\Reader;
use App\Models\order\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderItemDetails;
use Illuminate\Support\Facades\Storage;
use App\Models\order\OrderSellerCredentials;
use Carbon\Carbon;
use JeroenNoten\LaravelAdminLte\View\Components\Widget\Card;

class OrdersCsvImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:order-csv-import {store_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'csv file import for Orders';

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
        $store_id = $this->argument('store_id');
        // $store_id = 44;
        $country = OrderSellerCredentials::where('seller_id', $store_id)->get();
        $country_code = $country[0]->country_code;

        $file_path = "OrderFile/order.csv";
        $csv_data = Reader::createFromPath(Storage::path($file_path, 'r'));
        $csv_data->setDelimiter(',');
        $csv_data->setHeaderOffset(0);
        $orderItemDetails_table = [];
        $orders_table = [];
        $item_price = [];
        $shipping_address = [];
        $buyer_info = [];

        foreach ($csv_data as $key => $csv) {
            if ($key != 0) {

                $item_price = [
                    'CurrencyCode' => $csv['Currency_code'],
                    'Amount'       => $csv['Item_price'],
                ];

                $shipping_address = [
                    'Name'           =>  $csv['Buyer_name'],
                    'AddressLine1'   =>  $csv['Address_line_1'],
                    'AddressLine2'   =>  $csv['Address_line_2'],
                    'City'           =>  $csv['City'],
                    'StateOrRegion'  =>  $csv['State_or_region'],
                    'PostalCode'     =>  $csv['Postal_code'],
                    'CountryCode'    =>  $country_code,
                    'Phone'          =>  $csv['Phone'],
                    'AddressType'    =>  $csv['Address_type'],
                ];

                $shipping_address1 = [
                    'Name'           =>  $csv['Buyer_name'],
                    'AddressLine1'   =>  $csv['Address_line_1'],
                    'AddressLine2'   =>  $csv['Address_line_2'],
                    'City'           =>  $csv['City'],
                    'County'         =>  $csv['County'],
                    'CountryCode'    =>  $country_code,
                    'Phone'          =>  $csv['Phone'],
                    'AddressType'    =>  $csv['Address_type'],
                ];

                $buyer_info = [
                    'BuyerEmail' => $csv['Buyer_info'],
                ];

                $title = str_replace("'\'", "Or", $csv['Title']);
                // $title = $csv['Title'];

                $orderItemDetails_table[] = [

                    'seller_identifier'         => $store_id,
                    'country'                   => $country_code,
                    'amazon_order_identifier'   => $csv['Amazon_order_identifier'],
                    'asin'                      => $csv['ASIN'],
                    'seller_sku'                => $csv['Seller_sku'],
                    'order_item_identifier'     => $csv['Order_item_id'],
                    'title'                     => htmlspecialchars($title),
                    'quantity_ordered'          => $csv['Quantity_ordered'],
                    'quantity_shipped'          => $csv['Quantity_shipped'],
                    'item_price'                => json_encode($item_price),
                    'shipping_address'          => json_encode((($country_code == 'IN') ? $shipping_address : $shipping_address1)),
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ];

                $purchase_date = Carbon::parse($csv['Purchase_date'])->format('Y-m-d\Th:i:s\Z');
                $esd = Carbon::parse($csv['Earliest_ship_date'])->format('Y-m-d\Th:i:s\Z');
                $lsd = Carbon::parse($csv['Latest_ship_date'])->format('Y-m-d\Th:i:\Z');
                $edd = Carbon::parse($csv['Earliest_delivery_date'])->format('Y-m-d\Th:i:\Z');
                $ldd = Carbon::parse($csv['Latest_delivery_date'])->format('Y-m-d\Th:i:\Z');

                $orders_table[] = [

                    'our_seller_identifier'            =>  $store_id,
                    'country'                          =>  $country_code,
                    'amazon_order_identifier'          =>  $csv['Amazon_order_identifier'],
                    'purchase_date'                    =>  $purchase_date,
                    'order_status'                     =>  $csv['Order_status'],
                    'fulfillment_channel'              =>  $csv['Fulfillment_channel'],
                    'sales_channel'                    =>  $csv['Sales_channel'],
                    'payment_method_details'           =>  $csv['Payment_method_identifier'],
                    'number_of_items_unshipped'        =>  $csv['Number_of_items_unshipped'],
                    'marketplace_identifier'           =>  $csv['Marketplace_id'],
                    'ship_service_level'               =>  $csv['Ship_service_level'],
                    'order_total'                      =>  json_encode($item_price),
                    'number_of_items_shipped'          =>  $csv['Quantity_shipped'],
                    'shipment_service_level_category'  =>  $csv['Shipment_service_level_category'],
                    'order_type'                       =>  $csv['Order_type'],
                    'earliest_ship_date'               =>  $esd,
                    'latest_ship_date'                 =>  $lsd,
                    'earliest_delivery_date'           =>  $edd,
                    'latest_delivery_date'             =>  $ldd,
                    'shipping_address'                 =>  json_encode((($country_code == 'IN') ? $shipping_address : $shipping_address1)),
                    'buyer_info'                       =>  json_encode($buyer_info),
                    'created_at'                       =>  now(),
                    'updated_at'                       =>  now(),
                ];
            }
        }
        OrderItemDetails::upsert(
            $orderItemDetails_table,
            ['order_item_identifier_UNIQUE'],
            [
                'seller_identifier',
                'country',
                'amazon_order_identifier',
                'asin',
                'seller_sku',
                'title',
                'quantity_ordered',
                'quantity_shipped',
                'item_price',
                'shipping_address'
            ]
        );

        Order::upsert(
            $orders_table,
            ['amazon_order_identifier_UNIQUE'],
            [
                'our_seller_identifier',
                'country',
                'fulfillment_channel',
                'amazon_order_identifier',
                'purchase_date',
                'order_status',
                'fulfillment_channel',
                'sales_channel',
                'payment_method_details',
                'number_of_items_unshipped',
                'marketplace_identifier',
                'ship_service_level',
                'order_total',
                'number_of_items_shipped',
                'shipment_service_level_category',
                'order_type',
                'earliest_ship_date',
                'latest_ship_date',
                'earliest_delivery_date',
                'latest_delivery_date',
                'shipping_address',
                'buyer_info',
            ]
        );
    }
}
