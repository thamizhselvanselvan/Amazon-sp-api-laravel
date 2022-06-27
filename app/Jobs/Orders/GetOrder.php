<?php

namespace App\Jobs\Orders;

use App\Services\SP_API\API\Order\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetOrder implements ShouldQueue
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
        $awsId = $this->payload['aws_id'];
        $awsCountryCode = $this->payload['country_code'];
        $seller_id = $this->payload['seller_id'];
        $auth_code = NULL;
        $order = new Order();
        $order->SelectedSellerOrder($awsId, $awsCountryCode, $auth_code, $seller_id);
    }
}
