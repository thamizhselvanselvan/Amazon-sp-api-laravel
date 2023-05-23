<?php

namespace App\Jobs\BusinessAPI;

use App\Models\Business\Catalog;
use RedBeanPHP\R;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AWS_Business_API\Details_dump\b_api_productdetailsdump;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;

class BusinessasinDetails implements ShouldQueue
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
        $asin =   $this->payload['data'];
        $booking = new b_api_productdetailsdump();
        $responce = $booking->savedetails($asin);
   
    }
}
