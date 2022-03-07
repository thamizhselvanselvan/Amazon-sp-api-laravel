<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\SP_API\CatalogImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AmazonCatalogImport implements ShouldQueue
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
        Log::alert("Alert from Jobs");
       

        // $asin = $this->payload['asin'];
        // $country_code = $this->payload['country_code'];
        // $auth_code =  $this->payload['auth_code'];
        // $aws_key = $this->payload['aws_key'];

        // Log::alert($asin,$country_code,$auth_code,$aws_key);

        $amazonCatalogsImport = new CatalogImport();
        $amazonCatalogsImport->amazonCatalogImport( $this->payload['asin'], $this->payload['country_code'], $this->payload['auth_code'], $this->payload['aws_key']);
    }
}
