<?php

namespace App\Services\AWS_Business_API\Details_dump;

use RedBeanPHP\R;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;

class product_details
{

    public function savedetails($asin)
    {
        $start_time = startTime();
        $end_time = endTime($start_time);
        $rec =  $asin;
        foreach ($rec as $val) {
            $fetched[] = ($val->asin);
        }

        $ApiCall = new ProductsRequest();
        $counter = 1;
        foreach ($fetched as $data) {
            $asin = $data;
            $data = $ApiCall->getASINpr($asin);

            $asin = '';
            $asin_type = '';
            $signedProductId = '';
            $offers = '';
            $availability = '';
            $buyingGuidance = '';
            $fulfillmentType = '';
            $merchant = '';
            $offerId = '';
            $price = '';
            $listPrice = '';
            $productCondition = '';
            $condition = '';
            $quantityLimits = '';
            $deliveryInformation = '';
            $features = '';
            $taxonomies = '';
            $title = '';
            $url = '';
            $productOverview = '';
            $productVariations = '';



            if (property_exists($data, "errors") && $data->errors[0]->code == "PRODUCT_NOT_FOUND") {
                $asin = 'Not Found';
                $asin_type = 'Not Found';
                $signedProductId  = 'Not Found';
                $offers = 'Not Found';
                $availability = 'Not Found';
                $buyingGuidance = 'Not Found';
                $fulfillmentType = 'Not Found';
                $merchant = 'Not Found';
                $offerId = 'Not Found';
                $price = 'Not Found';
                $listPrice = 'Not Found';
                $productCondition = 'Not Found';
                $condition = 'Not Found';
                $quantityLimits = 'Not Found';
                $deliveryInformation = 'Not Found';
                $features = 'Not Found';
                $taxonomies = 'Not Found';
                $title = 'Not Found';
                $url = 'Not Found';
                $productOverview = 'Not Found';
                $productVariations = 'Not Found';
            } else if (property_exists($data, "errors") && $data->errors[0]->message == "You exceeded your quota for the requested resource.") {
                // Log::warning('429');
                sleep(10);
                break;
            } else {
                if (isset($data->asin)) {


                    $asin = ($data->asin);

                    $asin_type = ($data->asinType);
                    $signedProductId  = ($data->signedProductId);
                    if ($data->includedDataTypes->OFFERS == []) {
                        $offers = 'null';
                        $availability = 'null';
                        $buyingGuidance = 'null';
                        $fulfillmentType = 'null';
                        $merchant = 'null';
                        $offerId = 'null';
                        $price = 'null';
                        $listPrice = 'null';
                        $productCondition = 'null';
                        $condition = 'null';
                        $quantityLimits = 'null';
                        $deliveryInformation = 'null';
                    } else {
                        Log::notice('unmatched');
                    }
                } else   if (property_exists($data, "errors") && $data->errors[0]->code == "You exceeded your quota for the requested resource.") {
                    $end_time = endTime($start_time);
                    Log::info("After this $counter much request 429 error came. timing $end_time");
                } else {
                    $offers = json_decode(json_encode($data->includedDataTypes->OFFERS[0]));

                    $availability = ($offers->availability);
                    $buyingGuidance = ($offers->buyingGuidance);
                    $fulfillmentType = ($offers->fulfillmentType);
                    $merchant = json_encode($offers->merchant);
                    $offerId = ($offers->offerId);
                    $price = json_encode($offers->price);
                    $listPrice = json_encode($offers->listPrice);
                    $productCondition = ($offers->productCondition);
                    $condition = json_encode($offers->condition);
                    $quantityLimits = json_encode($offers->quantityLimits);
                    $deliveryInformation = ($offers->deliveryInformation);
                }
                $features = json_encode($data->features);
                $taxonomies = json_encode($data->taxonomies);
                $title = ($data->title);
                $url = ($data->url);
                $productOverview = json_encode($data->productOverview);
                $productVariations = json_encode($data->productVariations->variations);
            }

            DB::connection('mongodb')->table('catalog_details_bapi')->where('asin', $asin)->update(
                [
                    'asin' =>      $asin,
                    'asin_type' => $asin_type,
                    'signedProductid' => $signedProductId,
                    'availability' => $availability,
                    'buyingGuidance' => $buyingGuidance,
                    'fulfillmentType' =>  $fulfillmentType,
                    'merchant' => $merchant,
                    'offerid' =>  $offerId,
                    'price' =>      $price,
                    'listPrice' =>   $listPrice,
                    'productCondition' =>  $productCondition,
                    'condition' => $condition,
                    'quantityLimits' =>  $quantityLimits,
                    'deliveryInformation' =>  $deliveryInformation,
                    'features' => $features,
                    'taxonomies' => $taxonomies,
                    'title' =>    $title,
                    'url' =>    $url,
                    'productOverview' =>   $productOverview,
                    'productvariations' =>   $productVariations,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at'  => now()->format('Y-m-d H:i:s')
                ],
                ["upsert" => true]
            );
            $end_time = endTime($start_time);

            $counter++;
        }
        $finished_loop = endTime($start_time);
        Log::info("FInal Query Time and finished at $finished_loop");
    }
}
