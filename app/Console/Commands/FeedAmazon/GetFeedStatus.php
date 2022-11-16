<?php

namespace App\Console\Commands\FeedAmazon;

use App\Models\order\OrderUpdateDetail;
use Illuminate\Console\Command;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

class GetFeedStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:feed-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Awb Feed Status';

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

    use ConfigTrait;
    public function handle()
    {
        $seller_id = '6';
        $feed_id = '129897019312';
        // $feed_id = '129877019312';
        $feed_details = OrderUpdateDetail::where([
            ['order_status', '!=', 'unshipped'],
            ['order_feed_status', NULL]

        ])->get(['order_status', 'store_id']);

        $groups = $feed_details->groupBy('store_id');

        if ($feed_details->isEmpty()) {
            return false;
        }

        foreach ($groups as $group) {
            $results[] = head($group);
        }

        po($results);
        exit;

        po($feed_details);
        exit;

        $url  = (new FeedOrderDetailsApp360())->getFeedStatus($feed_id, $seller_id);

        $data = file_get_contents($url);
        $data_json = json_decode(json_encode(simplexml_load_string($data)), true);
        $report = $data_json['Message']['ProcessingReport'];
        $success_message = $report['ProcessingSummary']['MessagesSuccessful'];

        $msg = '';
        if ($success_message == 1) {
            $msg = $success_message;
        } else {
            $msg = $report['Result']['ResultDescription'];
        }

        OrderUpdateDetail::where([
            ['order_status', $feed_id]
        ])->update(['order_feed_status' => $msg]);
    }
}
