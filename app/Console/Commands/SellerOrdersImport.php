<?php

namespace App\Console\Commands;


use App\Jobs\Orders\GetOrder;
use App\Models\Aws_credential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
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
        $aws_data = OrderSellerCredentials::where('dump_order', 1)->get();
        foreach ($aws_data as $aws_value) {
            
            // $awsId  = $aws_value['id'];
            $awsCountryCode = $aws_value['country_code'];
            $seller_id = $aws_value['seller_id'];
            $auth_code = NULL;
            
            if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
    
                GetOrder::dispatch(
                    [
                        'country_code' => $awsCountryCode,
                        'seller_id' =>$seller_id
                     ]
                )->onConnection('redis')->onQueue('default');
            } else {
                GetOrder::dispatch(
                    [
                        'country_code' => $awsCountryCode,
                        'seller_id' => $seller_id
                    ]
                );
            }
        }

    }
}
