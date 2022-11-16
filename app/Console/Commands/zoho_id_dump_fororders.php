<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderUpdateDetail;

class zoho_id_dump_fororders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho-id-fetch';

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
            ->whereIn('store_id', [5, 6])
            ->chunk(100, function ($result) {

                $table_nitrous = 'nitrous_amazon_order_details';
                $table_mbm = 'in_mbm_amazon_order_details';
                $nitrous_order_id = [];
                $mbm_order_id = [];
                foreach ($result as $data) {
                    $store = $data->store_id;

                    if ($store === '6') {
                        $nitrous_order_id[] = $data['order_item_id'];
                    } else if ($store === '5') {
                        $mbm_order_id[] = $data['order_item_id'];
                    }
                }
                $zoho_nitrous_data[] = DB::connection('aws')->table($table_nitrous)
                    ->select('amazon_order_id', 'order_item_id', 'zoho_id')
                    ->whereIn('order_item_id', $nitrous_order_id)
                    ->get();

                $zoho_mbm_data[] = DB::connection('aws')->table($table_mbm)
                    ->select('amazon_order_id', 'order_item_id', 'zoho_id')
                    ->whereIn('order_item_id', $mbm_order_id)
                    ->get();

                $this->insert_zohoid($zoho_nitrous_data);
                $this->insert_zohoid($zoho_mbm_data);
            });
    }
    function insert_zohoid($zoho_nitrous_data)
    {
        foreach ($zoho_nitrous_data as $data) {

            if (count($data) == '0') {
                Log::alert('no data Found In AWS  table');
            } else {
                $values = (json_decode(json_encode($data)));
                foreach ($values as $index => $key) {
                    $data_forated = ($key);

                    $zoho_id = str_replace('zcrm_', '', ($data_forated->zoho_id));
                    $order_item_id =  $data_forated->order_item_id;
                    $update = ['zoho_id' => $zoho_id];

                    OrderUpdateDetail::query()
                        ->where('order_item_id', $order_item_id)
                        ->update($update);
                }
            }
        }
    }
}
