<?php

namespace App\Jobs\Seller;

use Illuminate\Bus\Queueable;
use App\Services\SP_API\API\Catalog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class Seller_catalog_import_job implements ShouldQueue
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
        $datas = $this->payload['datas'];
        $type = $this->payload['type'];
        $seller_id = NULL;
        $catalog =   new Catalog();
        //$type = 1 for seller, 2 for Order, 3 for inventory
        // $type = 1;
        $catalogApi = $catalog->index($datas, $seller_id, $type);
    }
}
