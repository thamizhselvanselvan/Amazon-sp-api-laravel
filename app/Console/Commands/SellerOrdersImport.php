<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SellerOrdersImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:sellers-orders-import {seller-id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Seller orders from Amazon according to seller id';

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
       $seller_id = $this->argument('seller-id');
       echo $seller_id;
    }
}
