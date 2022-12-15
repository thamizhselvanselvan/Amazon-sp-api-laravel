<?php

namespace App\Console\Commands\Orders;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Order\Order;
use App\Models\order\OrderSellerCredentials;
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
        //Process Management start
        $process_manage = [
            'module'             => 'Order_item_details',
            'description'        => 'Import order item details for each order',
            'command_name'       => 'mosh:order-item-details-import',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];
        // $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end

        $order_item = new OrderItem();

        $seller_id_array = OrderSellerCredentials::where('dump_order', 1)->get();
        $missing_order_id = [];

        foreach ($seller_id_array as $value) {

            $seller_id = $value->seller_id;
            $zoho = $value->zoho;
            $courier_partner = $value->courier_partner;
            $source = $value->source;

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
                    order by ord.id asc
                    limit 1
                ");


            foreach ($missing_order_id as $details) {

                $country = $details->country;
                $order_id = $details->amazon_order_identifier;
                $aws_id = $details->our_seller_identifier;

                $order_item->OrderItemDetails($order_id, $aws_id, $country, $source, $zoho, $courier_partner);
            }
        }

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
        Log::notice($pm_id . '=> mosh:order-item-details-import');
    }
}
