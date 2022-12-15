<?php

namespace App\Console\Commands;


use App\Jobs\Orders\GetOrder;
use App\Models\Aws_credential;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Order\Order;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;

class SellerOrdersImport extends Command
{
    use ConfigTrait;
    public $seller_id;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:sellers-orders-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Seller orders from Amazon for selected seller';

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
        //Process Management start
        $process_manage = [
            'module'             => 'seller_order_import',
            'description'        => 'Get seller orders from Amazon of selected seller',
            'command_name'       => 'pms:sellers-orders-import',
            'command_start_time' => now(),
        ];

        ProcessManagement::create($process_manage);
        $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end

        $startTime = startTime();

        $aws_data = OrderSellerCredentials::where('dump_order', 1)->get();

        foreach ($aws_data as $aws_value) {

            $awsCountryCode = $aws_value['country_code']; //Destination
            $source = $aws_value['source'];
            $seller_id = $aws_value['seller_id'];
            $auth_code = NULL;
            $amazon_order_id = NULL;
            $order = new Order();
            $order->SelectedSellerOrder($seller_id, $awsCountryCode, $source, $auth_code, $amazon_order_id);
        }

        $stats = endTime($startTime);

        Log::info("pms:sellers-orders-import took $stats seconds");

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
        Log::notice($pm_id . '=> pms:sellers-orders-import');
    }
}
