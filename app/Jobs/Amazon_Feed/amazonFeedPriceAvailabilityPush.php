<?php

namespace App\Jobs\Amazon_Feed;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\AmazonFeedApiServices\AmazonFeedProcess;

class AmazonFeedPriceAvailabilityPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;
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
        Log::info($this->payload);

        $feedLists = $this->payload['feedLists'];
        $seller_id = $this->payload['seller_id'];
        $availability = $this->payload['availability'];

        if($availability) { // if condition is true then Update Availability

            (new AmazonFeedProcess)->feedSubmit($feedLists, $seller_id, true);
     
        } else  { // Otherwise Update only price

            (new AmazonFeedProcess)->feedSubmit($feedLists, $seller_id, false);
        }

    }
}
