<?php

namespace App\Jobs\B2C;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Services\B2CShip\B2cshipBooking;
use App\Services\Courier_Booking\B2cshipBookingServices;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CourierBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $amazon_order_id = $this->payload['amazon_order_id'];
        $order_item_id = $this->payload['order_item_id'];
        $courier_class_name = $this->payload['courier_class'];

        $booking = new B2cshipBookingServices();
        $booking->b2cdata($amazon_order_id, $order_item_id);
    }
}
