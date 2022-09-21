<?php

namespace App\Console\Commands\Orders;

use League\Csv\Reader;
use App\Models\order\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderItemDetails;
use Illuminate\Support\Facades\Storage;
use App\Models\order\OrderSellerCredentials;

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
        $country = OrderSellerCredentials::where('seller_id', $store_id)->get();
        $country_code = $country[0]->country_code;
        
        $file_path = "OrderFile/order.csv";
        $csv_data = Reader::createFromPath(Storage::path($file_path, 'r'));
        $orderItemDetails_table = [];
        $orders_table = [];
        $item_price = [];
        $shipping_address = [];
        $buyer_info = [];

        foreach($csv_data as $key => $csv)
        {
            if($key != 0){

                $item_price = [
                    'CurrencyCode' => $csv[7],
                    'Amount' => $csv[8],
                ];

                $shipping_address = [
                    'Name'  =>  $csv[9],
                    'AddressLine1'  =>  $csv[10],
                    'AddressLine2'  =>  $csv[11],
                    'City'  =>  $csv[12],
                    'StateOrRegion' => $csv[13],
                    'PostalCode' => $csv[14],
                    'CountryCode' => $country_code,
                    'Phone' =>  $csv[16],
                    'AddressType' => $csv[17],
                ];

                $shipping_address1 = [
                    'Name'  =>  $csv[9],
                    'AddressLine1'  =>  $csv[10],
                    'AddressLine2'  =>  $csv[11],
                    'City'  =>  $csv[12],
                    'County' => $csv[15],
                    'CountryCode' => $country_code,
                    'Phone' =>  $csv[16],
                    'AddressType' => $csv[17],
                ];

                $buyer_info = [
                    'BuyerEmail' => $csv[32],
                ];

                $orderItemDetails_table [] = [

                    'seller_identifier' => $store_id,
                    'country'   => $country_code,
                    'amazon_order_identifier'   => $csv[0],
                    'asin'  => $csv[1],
                    'seller_sku' => $csv[2],
                    'order_item_identifier' => $csv[3],
                    'title' => $csv[4],
                    'quantity_ordered'  =>  $csv[5],
                    'quantity_shipped'  =>  $csv[6],
                    'item_price'    =>  json_encode($item_price),
                    'shipping_address'  =>  json_encode((($country_code == 'IN') ? $shipping_address : $shipping_address1)),
                    'created_at'    =>  now(),
                    'updated_at'    =>  now(),
                ];
                
                $orders_table [] = [

                    'our_seller_identifier' =>  $store_id,
                    'country'   =>  $country_code,
                    'amazon_order_identifier'   =>  $csv[0],
                    'purchase_date' =>  $csv[18],
                    'order_status'  =>  $csv[19],
                    'fulfillment_channel' =>  $csv[20],
                    'sales_channel' =>  $csv[21],
                    'payment_method_details' =>  $csv[22],
                    'number_of_items_unshipped' => $csv[23],
                    'marketplace_identifier'    =>  $csv[24],
                    'ship_service_level'    =>  $csv[25],
                    'order_total'   =>  json_encode($item_price),
                    'number_of_items_shipped' => $csv[6],
                    'shipment_service_level_category'    =>  $csv[26],
                    'order_type'    =>  $csv[27],
                    'earliest_ship_date'    =>  $csv[28],
                    'latest_ship_date'    =>  $csv[29],
                    'earliest_delivery_date'    =>  $csv[30],
                    'latest_delivery_date'    =>  $csv[31],
                    'shipping_address'  =>  json_encode((($country_code == 'IN') ? $shipping_address : $shipping_address1)),
                    'buyer_info'    =>  json_encode($buyer_info),
                    'created_at'    =>  now(),
                    'updated_at'    =>  now(),
                ];
            }
        }
        OrderItemDetails::upsert($orderItemDetails_table, ['order_item_identifier'], [
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
        ]);

        Order::upsert($orders_table, ['amazon_order_identifier'], [
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
        ]);
        
    }
    
}
