<?php

namespace App\Console\Commands\FeedAmazon;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetails;

class FeedTrackingDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:feed-tracking-details-to-amazon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'B2cship Tracking Details Feed to Amazon';

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
        Log::alert("Feed Command running");
        (new FeedOrderDetails())->FeedOrderTrackingNo();
    }
}
