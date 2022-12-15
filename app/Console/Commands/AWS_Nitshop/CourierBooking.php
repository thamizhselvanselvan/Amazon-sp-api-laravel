<?php

namespace App\Console\Commands\AWS_Nitshop;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
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
        //Process Management start
        $process_manage = [
            'module'             => 'AWS',
            'description'        => 'Courier Booking',
            'command_name'       => 'aws:courier-booking',
            'command_start_time' => now(),
        ];

        ProcessManagement::create($process_manage);
        $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end

        $order_details = OrderUpdateDetail::where([['courier_awb', NULL], ['courier_name', '!=', NULL], ['booking_status', '0']])
            ->limit(1)
            ->get(['amazon_order_id', 'order_item_id', 'courier_name', 'courier_awb', 'store_id']);

        if (count($order_details) > 0) {
            $order_id = $order_details[0]->amazon_order_id;
            $order_item_id = $order_details[0]->order_item_id;
            $courier_name = $order_details[0]->courier_name;
            $store_id = $order_details[0]->store_id;

            $job_parameters = [
                'amazon_order_id' => $order_id,
                'order_item_id' => $order_item_id,
                'courier_class' => $courier_name,
                'store_id' => $store_id
            ];

            OrderUpdateDetail::where(
                [
                    ['amazon_order_id', $order_id],
                    ['order_item_id', $order_item_id]
                ],
            )->update(['booking_status' => '5']);


            jobDispatchFunc('Courier_Booking\CourierBookingJob', $job_parameters);
        }


        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
        Log::notice($pm_id . '=> aws:courier-booking');
    }
}
