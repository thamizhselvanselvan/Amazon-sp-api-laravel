<?php

namespace App\Services\AWS_Business_API\Details_dump;

use RedBeanPHP\R;
use Illuminate\Support\Facades\Log;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;

class product_details
{

    public function savedetails($fetched)
    {
        $host = config('database.connections.business.host');
        $dbname = config('database.connections.business.database');
        $port = config('database.connections.business.port');
        $username = config('database.connections.business.username');
        $password = config('database.connections.business.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        $ApiCall = new ProductsRequest();

        foreach ($fetched as $data) {
            $asin = $data;

            $data = $ApiCall->getASINpr($asin);
            // $data = json_decode(json_encode($res));

            $asin = ($data->asin);
            $asin_type = ($data->asinType);
            $signedProductId  = ($data->signedProductId);
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
            $features = json_encode($data->features);
            $taxonomies = json_encode($data->taxonomies);
            $title = ($data->title);
            $url = ($data->url);
            $productOverview = json_encode($data->productOverview);
            $productVariations = json_encode($data->productVariations);


            $data = R::dispense('uscatalog');

            $data->asin = $asin;
            $data->asin_type = $asin_type;
            $data->signedProductid_ =  $signedProductId;
            $data->availability = $availability;
            $data->buyingGuidance = $buyingGuidance;
            $data->fulfillmentType =  $fulfillmentType;
            $data->merchant   =  $merchant;
            $data->offerid_ =  $offerId;
            $data->price =   $price;
            $data->listPrice = $listPrice;
            $data->productCondition = $productCondition;
            $data->condition =   $condition;
            $data->quantityLimits =  $quantityLimits;
            $data->deliveryInformation =  $deliveryInformation;
            $data->features =     $features;
            $data->taxonomies = $taxonomies;
            $data->title = $title;
            $data->url = $url;
            $data->productOverview =  $productOverview;
            $data->productOverview =  $productVariations;

            R::store($data);
            po($asin);
        }
    }
}
