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

class amazonFeedPriceAvailabilityPush implements ShouldQueue
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
        $feedLists = $this->payload['feedLists'];
        $seller_id = $this->payload['seller_id'];

        $amazonFeedProcess = new AmazonFeedProcess();
        $amazonFeedProcess->feedSubmit($feedLists, $seller_id);
    }
}
