<?php

namespace App\Support\BusinessAPI;

use Exception;
use AmazonBusinessApi\Endpoint;
use AmazonBusinessApi\Configuration;
use App\Support\BusinessAPI\BusinessAPI;
use AmazonBusinessApi\Api\ProductSearchV20200826Api;

class ProductSearch {

    public static function search($asin) {

        $api = new ProductSearchV20200826Api(BusinessAPI::config());

        try {
            $result = $api->productsRequest($asin, 'US', 'en_US', 'nitrouspurchases@gmail.com');

            $results = json_decode(json_encode($result));

            po($results);
         
        } catch (Exception $e) {
            echo 'Exception when calling ProductSearchApi->productsRequest: ', $e->getMessage(), PHP_EOL;
        }

    }

}