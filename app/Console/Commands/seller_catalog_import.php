<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Catalog;
use App\Models\seller\AsinMasterSeller;
use App\Jobs\Seller\Seller_catalog_import_job;
use App\Jobs\Seller\Seller_catalog_import as SellerSeller_catalog_import;

class seller_catalog_import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:seller-catalog-import {seller_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seller catalog import from amazon catalog API';

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
        // Log::warning('working');
        $seller_id = $this->argument('seller_id');
        $chunk = 10;
        // $seller_id = 20;
        // $datas = AsinMasterSeller::limit(10)->offset(0)->where('status', 0)->where('seller_id', $seller_id)->get();
        AsinMasterSeller::where('status', 0)->where('seller_id', $seller_id)->chunk($chunk, function ($datas) use ($seller_id) {
            //$type = 1 for seller, 2 for Order, 3 for inventory

            $catalog = new Catalog();
            $type = 1;
            $catalog->index($datas, $seller_id, $type);
        });
    }
}
