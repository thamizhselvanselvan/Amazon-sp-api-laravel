<?php

namespace App\Services\AmazonFeedApiServices;

use Exception;
use App\Models\Aws_credential;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\Config\ConfigTrait;
use App\Services\PMS_SP_API\API\ProductFeed;


class AmazonFeedProcess
{
    use ConfigTrait;

    public function feedSubmit($feedLists, $seller_id)
    {

        $aws = '';
        $aws_key = '';

        $aws = Aws_credential::where('seller_id', $seller_id)->where('api_type', 1)->with(['mws_region'])->first();
        Log::alert($aws);
        exit;
        $aws_key = $aws->id;
        $merchant_id = $aws->merchant_id;
        $mws_region = $aws->mws_region;
        $country_code = $mws_region->region_code;
        $currency_code = $mws_region->currency->code;
        $marketplace_id = $mws_region->marketplace_id;

        $productFeed = new ProductFeed;
        $feedSubmit = $productFeed->createFeedDocument($aws_key, $country_code, $feedLists, $merchant_id, $currency_code, [$marketplace_id]);
        Log::critical($feedSubmit);
        if (!$feedSubmit) {

            // event(new ProductImportCompleted($seller_id, "Your price push has failed check with admin"));
            throw new Exception('Feed submit showing error 1');
        }

        //event(new ProductImportCompleted($seller_id, "Your price push has submitted successfully"));
    }
}
