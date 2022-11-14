<?php

namespace App\Console\Commands\Zoho;

use Illuminate\Console\Command;
use App\Services\Zoho\ZohoOrder;

class Zoho extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho:save {amazon_order_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order details store it in Zoho CRM';

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

        $amazon_order_id = $this->argument('amazon_order_id');

        $zoho_order = new ZohoOrder;
        return $zoho_order->index($amazon_order_id);
    }
}
