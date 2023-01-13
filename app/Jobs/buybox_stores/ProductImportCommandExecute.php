<?php

namespace App\Jobs\buybox_stores;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProductImportCommandExecute implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $seller_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($seller_id)
    {
        $this->seller_id = $seller_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $seller_id = (int) $this->seller_id;

        // Artisan::call("pms:aws:sync $seller_id");

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            exec("nohup php artisan mosh:import_product_file $seller_id > /dev/null &");
        } else {
            Artisan::call("mosh:import_product_file $seller_id");
        }
    }
}
