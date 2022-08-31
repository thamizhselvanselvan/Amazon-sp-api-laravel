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

        $ApiCall = new ProductsRequest();

        // foreach ($fetched as $data) {
        //     $asin = $data;

        $data = $ApiCall->getASINpr($asin);

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
        } else {

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
            $productVariations = json_encode($data->productVariations);
        }

        DB::connection('mongodb')->table('product_details')->where('asin', $asin)->update(
            [
                'asin' => $asin,
                'asin_type' => $asin_type,
                'signedProductId ' => $signedProductId,
                'offers' => $offers,
                'availability' => $availability,
                'buyingGuidance' => $buyingGuidance,
                'fulfillmentType' => $fulfillmentType,
                'merchant' => $merchant,
                'offerId' => $offerId,
                'price' => $price,
                'listPrice' => $listPrice,
                'productCondition' => $productCondition,
                'condition' => $condition,
                'quantityLimits' => $quantityLimits,
                'deliveryInformation' => $deliveryInformation,
                'features' => $features,
                'taxonomies' => $taxonomies,
                'title' => $title,
                'url' => $url,
                'productOverview' => $productOverview,
                'productVariations' => $productVariations,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at'  => now()->format('Y-m-d H:i:s')
            ],
            ["upsert" => true]
        );


    }
}
