<?php

namespace App\Support\BusinessAPI;

use Exception;
use AmazonBusinessApi\Endpoint;
use AmazonBusinessApi\Configuration;
use App\Support\BusinessAPI\BusinessAPI;
use AmazonBusinessApi\Api\ProductSearchV20200826Api;
use AmazonBusinessApi\Model\ProductSearchV20200826\ProductsByAsinsRequest;

class ProductSearch {

    public static function search($asin) {

        $api = new ProductSearchV20200826Api(BusinessAPI::config());

        try {

            $x_amz_user_email = 'nitrouspurchases@gmail.com';
            $inclusions_for_products = null;
            $inclusions_for_offers = null;
            $group_tag = null;
            $products_by_asins = [$asin];

            $products_by_asins = new ProductsByAsinsRequest(
                [
                    "product_ids" => [$asin],
                    "product_region" => 'US',
                    "locale" => 'en_US',
                    "shipping_postal_code" => null,
                    "quantity" => null,
                    "facets" => ['OFFERS', 'IMAGES'],
                   // product_ids
// product_region
// locale
// shipping_postal_code
// quantity
// facets
            ]);


            $result = $api->getProductsByAsins($x_amz_user_email, $inclusions_for_products, $inclusions_for_offers, $group_tag, $products_by_asins);
           // $result = $api->productsRequest($asin, 'US', 'en_US', 'nitrouspurchases@gmail.com');

            $results = json_decode(json_encode($result));

            po($results);
         
        } catch (Exception $e) {
            echo 'Exception when calling ProductSearchApi->productsRequest: ', $e->getMessage(), PHP_EOL;
        }

    }

}