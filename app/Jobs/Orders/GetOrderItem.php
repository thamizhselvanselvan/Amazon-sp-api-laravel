<?php

namespace App\Jobs\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\SP_API\API\Order\OrderItem;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class GetOrderItem implements ShouldQueue
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
        Log::info("Order Item Import");
        $order_id = $this->payload['order_id'];
        $aws_id = $this->payload['aws_id'];
        $country_code = $this->payload['country_code'];
       

        $order_item = new OrderItem();
        $order_item->OrderItemDetails($order_id, $aws_id, $country_code);
    }
}
