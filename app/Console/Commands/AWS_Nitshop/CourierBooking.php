<?php

namespace App\Console\Commands\AWS_Nitshop;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderUpdateDetail;
use App\Services\B2CShip\B2cshipBooking;

class CourierBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aws:courier-booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Courier Booking';

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
        $order_details = OrderUpdateDetail::where([['courier_awb', NULL], ['courier_name', '!=', NULL]])
            ->limit(1)
            ->get(['amazon_order_id', 'order_item_id', 'courier_name', 'courier_awb']);

        if (count($order_details) > 0) {
            $order_id = $order_details[0]->amazon_order_id;
            $order_item_id = $order_details[0]->order_item_id;
            $courier_name = $order_details[0]->courier_name;

            $job_parameters = [
                'amazon_order_id' => $order_id,
                'order_item_id' => $order_item_id,
                'courier_class' => $courier_name
            ];

            jobDispatchFunc('Courier_Booking\CourierBookingJob', $job_parameters);
        }
    }
}
