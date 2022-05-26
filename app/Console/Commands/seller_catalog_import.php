<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
    protected $signature = 'pms:seller-catalog-import {login_id} {email}';

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
        $login_id = $this->argument('login_id');
        $email = $this->argument('email');

        $chunk = 10;
        $datas = AsinMasterSeller::limit(10)->offset(0)->where('status', 0)->where('seller_id', $login_id)->get();
        // $datas = AsinMasterSeller::chunk($chunk)->where('status', 0)->where('seller_id', $login_id)->get();
        Seller_catalog_import_job::dispatch(
            [
                'email' => $email,
                'datas' => $datas,
            ]
        );
        // $catalog =   new Catalog();
        // $catalogApi = $catalog->index($datas, $email);

    }
}
