<?php

namespace App\Console\Commands\Zoho;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderUpdateDetail;

class ZohoSlackNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho_slack_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command will get zoho status (which is 3) for previous 24 hours and send slack Notification ';

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
        $startTime = Carbon::now()->format('Y-m-d') . ' ' . "00:00:00";
        $endtime = Carbon::yesterday()->format('Y-m-d') . " 00:00:01";

        $datas =   OrderUpdateDetail::select(['amazon_order_id', 'order_item_id'])
            ->where('zoho_status', '3')
            ->whereBetween('created_at', [$endtime, $startTime])
            ->get();

        $order_no = [];
        $item_no = [];
        foreach ($datas as $data) {

            $order_no[] = ($data['amazon_order_id']);
            $item_no[] = ($data['order_item_id']);
        }
        if (count($order_no) > 0) {
            $slackMessage = 'Zoho Status is  3 for The Following  ' .
                'Amazon Order ID = ' . implode(" ", $order_no) . ' ' . 'And' .
                'Order Item Identifier = ' . implode(" ", $item_no);

            slack_notification('app360', 'Zoho Booking', $slackMessage);
        }
    }
}
