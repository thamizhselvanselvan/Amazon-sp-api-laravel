<?php

namespace App\Console\Commands\buybox_stores;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product_Push;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;

class GetFeedResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:get_stores_feed_response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get Feed ID from Product_Push Table and Update Feed Response';

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
        $datas = Product_Push::query()
            ->where('feedback_price_status', 0)
            ->select('feedback_price_id', 'store_id', 'asin')
            ->limit(10)
            ->get();

        if (count($datas) > 0) {

            foreach ($datas as $data) {

                try {

                    if (isset($data->feedback_price_id)) {
                        $feedback_id = $data->feedback_price_id;
                        $store_id = $data->store_id;
                        $asin = $data->asin;
                        $country = OrderSellerCredentials::where('seller_id', $store_id)->select('country_code')->get();
                        $country_code = 'IN';
                        $country_code = $country['0']->country_code;

                        $url  = (new FeedOrderDetailsApp360())->getFeedStatus($feedback_id, $store_id, $country_code);

                        if ($url) {

                            $data = file_get_contents($url);
                            $data_json = json_decode(json_encode(simplexml_load_string($data)), true);
                            $report = $data_json['Message']['ProcessingReport'];
                            $success_message = $report['ProcessingSummary']['MessagesSuccessful'];
                            $error_message = $report['ProcessingSummary']['MessagesWithError'];

                            $msg = '';
                            $status= '';
                            if ($success_message == 1) {
                                $msg = 'success';
                                $status = 1;
                            } else if ($error_message == 1) {
                                $msg = 'Failed';
                                $status = 2;
                            } else {
                                $msg = $report['Result']['ResultDescription'];
                            }
                            Product_Push::where(['store_id' => $store_id, 'asin' => $asin])
                                ->update(['feedback_response' => $msg, 'feedback_price_status' =>$status ]);
                        }
                    }
                } catch (Exception $e) {
                    Log::notice('stores feed Response Not Found');
                    Product_Push::where(['store_id' => $store_id, 'asin' => $asin])
                        ->update(['feedback_price_status' => '3']);
                }
            }
        }
    }
}
