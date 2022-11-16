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
        OrderUpdateDetail::where([
            ['order_status', '!=', 'unshipped'],
            ['order_feed_status', NULL]

        ])->chunk(1, function ($result) {

            foreach ($result as $value) {

                $feed_id = $value->order_status;
                $seller_id = $value->store_id;
                $amazon_order_id = $value->amazon_order_id;
                $order_item_id = $value->order_item_id;

                $url  = (new FeedOrderDetailsApp360())->getFeedStatus($feed_id, $seller_id);

                $data = file_get_contents($url);
                $data_json = json_decode(json_encode(simplexml_load_string($data)), true);
                $report = $data_json['Message']['ProcessingReport'];
                $success_message = $report['ProcessingSummary']['MessagesSuccessful'];

                $msg = '';
                if ($success_message == 1) {
                    $msg = 'success';
                } else {
                    $msg = $report['Result']['ResultDescription'];
                }

                OrderUpdateDetail::where([
                    [
                        ['order_status' => $feed_id],
                        ['amazon_order_id' => $amazon_order_id],
                        ['order_item_id' => $order_item_id]
                    ]
                ])->update(['order_feed_status' => $msg]);
            }
        });
    }
}
