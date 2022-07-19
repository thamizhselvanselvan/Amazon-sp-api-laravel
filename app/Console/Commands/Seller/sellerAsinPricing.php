<?php

namespace App\Console\Commands\Seller;

use Illuminate\Console\Command;

class sellerAsinPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:seller-asin-get-pricing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Asin Buy Box price';

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
       
        
    }
}
