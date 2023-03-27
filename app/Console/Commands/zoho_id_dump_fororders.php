<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderUpdateDetail;
use App\Services\Zoho\ZohoApi;

class zoho_id_dump_fororders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho-id-fetch-from-crm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command will fetch all zoho ids from AWS Table and insert into order update table';

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

        $datas[] = OrderUpdateDetail::query()
            ->select('store_id', 'amazon_order_id', 'order_item_id')
            ->whereIn('store_id', [20])
            ->whereNull('zoho_id')
            ->chunk(100, function ($amazon) {

                foreach ($amazon as $key => $result) {

                    $amazon_order_id = $result->amazon_order_id;
                    $order_item_id = $result->order_item_id;

                    msg($amazon_order_id);
                    msg($order_item_id);

                    $zohoApi = new ZohoApi(new_zoho: false);
                    $zoho_search_order_exists = $zohoApi->search($amazon_order_id, $order_item_id);

                    if ($zoho_search_order_exists) {

                        $zoho_id = $zoho_search_order_exists['data'][0]['id'];

                        $update = [
                            'zoho_id' => $zoho_id,
                            'zoho_status' => 1
                        ];

                        $answer = OrderUpdateDetail::query()
                            ->where('order_item_id', $order_item_id)
                            ->where('amazon_order_id', $amazon_order_id)
                            ->update($update);
                    }
                }
            });
    }
}
