<?php

namespace App\Services\SP_API;

use Exception;
use SellingPartnerApi\Endpoint;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogApi as Catalog;

class CatalogAPI {

    public function getASIN($asin)
    {

        //  $asin = 'B07YM61M5P';
        $token = "Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
        $marketplace = 'ATVPDKIKX0DER'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
        $endpoint = Endpoint::NA;

        $config = new Configuration([
            "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
            "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
            "lwaRefreshToken" => $token,
            "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
            "awsSecretAccessKey" => "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",
            "endpoint" => $endpoint,  // or another endpoint from lib/Endpoints.php
            "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
        ]);
        $item_type = 'Asin'; // string | Indicates whether ASIN values or seller SKU values are used to identify items. If you specify Asin, the information in the response will be dependent on the list of Asins you provide in the Asins parameter. If you specify Sku, the information in the response will be dependent on the list of Skus you provide in the Skus parameter.
        $skus = []; // string[] | A list of up to twenty seller SKU values used to identify items in the given marketplace.
        $item_condition = 'New'; // string | Filters the offer listings based on item condition. Possible values: New, Used, Collectible, Refurbished, Club.
        $offer_type = 'B2C'; // string | Indicates whether to request pricing information for the seller's B2C or B2B offers. Default is B2C.
  
        $apiInstance = new Catalog($config);
     
        try {
            $result = $apiInstance->getCatalogItem($asin, $marketplace);
            $result = json_decode(json_encode($result));
            //  print_r($result->asin);
            //  print_r($result->asin.' '.$result->summaries[0]->itemName);
            
            return ['asin1' => $result->asin, 'item_name' => $result->summaries[0]->itemName];
            
        } catch (Exception $e) {
            
        }

        return ['error' => true];
    }

}