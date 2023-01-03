<?php

namespace App\Console\Commands\Orders;

use Illuminate\Console\Command;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\API\Order\CheckStoreCredServices;


class CheckStoreCreds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:check-store-creds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Store credentials';

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
        $aws_data = OrderSellerCredentials::where('dump_order', 1)
            ->get(['id', 'seller_id', 'country_code', 'cred_status', 'store_name'])
            ->toArray();

        foreach ($aws_data as $value) {

            (new CheckStoreCredServices())->index($value);
        }
    }
}
