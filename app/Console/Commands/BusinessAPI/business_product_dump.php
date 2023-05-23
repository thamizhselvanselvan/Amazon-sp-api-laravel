<?php

namespace App\Console\Commands\BusinessApI;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Jobs\BusinessAPI\BusinessasinDetails;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;

class business_product_dump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:business_api_catalog_dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Will get The Details of the Products of US Using Business API';

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
        $delay = 5;
        $start_time = startTime();
        $end_time = endTime($start_time);

        $records = table_model_create(country_code: 'US', model: 'Asin_source', table_name: 'asin_source_')
            ->select('asin')
            ->chunk(20, function ($records) use ($delay, $start_time) {
               
                if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
                    BusinessasinDetails::dispatch([
                        'data' => $records
                    ])->onconnection('redis')->delay($delay);
                } else {
                    BusinessasinDetails::dispatch(
                        [
                            'data' => $records,
                        ]

                    )->delay($delay);
                }

                $delay += $delay;
            });
    }
}
