<?php

namespace App\Console\Commands\AWS_Nitshop;

use Illuminate\Console\Command;
use App\Services\AWS_Nitshop\Index;

class Order_details extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aws:nitshop:order_details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $order = new Index;
        $order->order_details();
    }
}