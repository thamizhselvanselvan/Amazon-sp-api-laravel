<?php

namespace App\Jobs\catalog;

use RedBeanPHP\R;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Catalog;
use App\Services\SP_API\API\MongodbCatalog;
use App\Services\SP_API\CatalogImport;
use Illuminate\Queue\SerializesModels;
use App\Services\SP_API\API\NewCatalog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AmazonCatalogImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $payload;

    public $timeout = 60 * 2;

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
        $catalog_asin = $this->payload;
        $catalog = new NewCatalog();
        $catalog->Catalog($catalog_asin, $seller_id = NULL);

        $mongodb = new MongodbCatalog();    //Object of calss(or Instance)
        $mongodb->index($catalog_asin, $seller_id = NULL);
    }
}
