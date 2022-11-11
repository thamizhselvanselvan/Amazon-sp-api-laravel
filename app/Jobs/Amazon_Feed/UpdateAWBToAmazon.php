<?php

namespace App\Jobs\Amazon_Feed;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetails;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

class UpdateAWBToAmazon implements ShouldQueue
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
        $store_id = $this->payload['store_id'];
        $amazon_order_id = $this->payload['amazon_order_id'];
        $order_item_id = $this->payload['order_item_id'];
        $courier_name = $this->payload['courier_name'];
        $courier_awb = $this->payload['courier_awb'];

        Log::info($this->payload);

        (new FeedOrderDetailsApp360())->FeedOrderTrackingNo($store_id, $amazon_order_id, $order_item_id, $courier_name, $courier_awb);
    }
}
