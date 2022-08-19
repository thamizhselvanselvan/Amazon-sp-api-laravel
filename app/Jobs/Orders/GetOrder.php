<?php

namespace App\Jobs\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Services\SP_API\API\Order\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class GetOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $payload;
    private $timeout = 100;
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
        $awsCountryCode = $this->payload['country_code'];
        $seller_id = $this->payload['seller_id'];
        $amazon_order_id = $this->payload['amazon_order_id'];
        $auth_code = NULL;
        $order = new Order();
        $order->SelectedSellerOrder($seller_id, $awsCountryCode, $auth_code, $amazon_order_id);
    }
}
