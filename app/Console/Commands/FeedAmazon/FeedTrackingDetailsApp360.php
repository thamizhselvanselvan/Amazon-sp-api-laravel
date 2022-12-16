<?php

namespace App\Console\Commands\FeedAmazon;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderUpdateDetail;

class FeedTrackingDetailsApp360 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:feed-app360-tracking-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Feed Tracking Details To Amazon From App360';

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
        $process_manage = [
            'module'             => 'Feed_tracking',
            'description'        => 'Feed tracking details to Amazon from app360',
            'command_name'       => 'mosh:feed-app360-tracking-details',
            'command_start_time' => now(),
        ];
        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $data = OrderUpdateDetail::whereNotNUll('courier_awb')
            ->whereNotNull('courier_name')
            ->where('order_status', 'unshipped')
            ->get(['id', 'store_id', 'amazon_order_id', 'order_item_id', 'courier_name', 'courier_awb']);
        $groups = $data->groupBy('store_id');

        if ($data->isEmpty()) {
            return false;
        }

        $results = [];
        foreach ($groups as $group) {
            $results[] = head($group);
        }

        $store_details = [];
        foreach ($results as $value) {
            $value = $value[0];
            $store_details['store_id'] = $value['store_id'];
            $store_details['amazon_order_id'] = $value['amazon_order_id'];
            $store_details['order_item_id'] = $value['order_item_id'];
            $store_details['courier_name'] = $value['courier_name'];
            $store_details['courier_awb'] = $value['courier_awb'];
        }

        $class = 'Amazon_Feed\UpdateAWBToAmazon';

        $command_end_time = now();
        Log::notice('Before Dispatch time -> ' . $command_end_time);

        jobDispatchFunc($class, $store_details);

        ProcessManagementUpdate($pm_id, $command_end_time);

        Log::notice('Command End time -> ' . $command_end_time);
    }
}
