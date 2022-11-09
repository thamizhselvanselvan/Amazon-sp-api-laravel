<?php

namespace App\Console\Commands\Orders;

use App\Models\order\OrderSellerCredentials;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Order\Order;
use App\Services\SP_API\API\Order\OrderItem;

class OrderItemDetailsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:order-item-details-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Order item details for each order';

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
        $order_item = new OrderItem();
        // $order_item->OrderItemDetails('402-6119301-9353145', 35, 'AE');
        // exit;
        // $seller_id_array  = DB::connection('order')->select('SELECT our_seller_identifier 
        // from orders group by our_seller_identifier');

        // $tem_seller_id = [];
        // foreach ($seller_id_array as $key => $value) {
        //     $tem_seller_id[$key] = $value->our_seller_identifier;
        // }

        $seller_id_array = OrderSellerCredentials::where('dump_order', 1)->get();

        foreach ($seller_id_array as $value) {
            $seller_id = $value->seller_id;
            $zoho = $value->zoho;
            $courier_partner = $value->courier_partner;

            if ($seller_id != 44) {

                $missing_order_id = DB::connection('order')
                    ->select("SELECT ord.amazon_order_identifier, ord.our_seller_identifier, ord.country
                    from orders as ord
                            left join 
                        orderitemdetails as oids on ord.amazon_order_identifier = oids.amazon_order_identifier 
                    where
                        oids.amazon_order_identifier IS NULL 
                            AND ord.our_seller_identifier = '$seller_id' 
                            AND ord.order_status != 'Pending' 
                            AND ord.order_status != 'Canceled' 
                    order by ord.id desc
                    limit 1
                ");

                foreach ($missing_order_id as $details) {

                    $country = $details->country;
                    $order_id = $details->amazon_order_identifier;
                    $aws_id = $details->our_seller_identifier;

                    Log::info("Command $order_id -> $aws_id");

                    $order_item->OrderItemDetails($order_id, $aws_id, $country, $zoho, $courier_partner);
                }
            }
        }
    }
}
