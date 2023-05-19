<?php

namespace App\Support\BusinessAPI;

use Exception;
use AmazonBusinessApi\Endpoint;
use AmazonBusinessApi\Configuration;
use App\Support\BusinessAPI\BusinessAPI;
use AmazonBusinessApi\Api\ProductSearchV20200826Api;
use AmazonBusinessApi\Model\ProductSearchV20200826\ProductsByAsinsRequest;

class ProductSearch {

    public static function search_1($asin) {

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

            return ($results);
         
        } catch (Exception $e) {
            echo 'Exception when calling ProductSearchApi->productsRequest: ', $e->getMessage(), PHP_EOL;
        }

    }

    public static function search_offers($asin) {

        $apiInstance = new ProductSearchV20200826Api(BusinessAPI::config());
        $product_id = $asin; // string | The Amazon Standard Item Identifier (ASIN) for the product for which to get offers.
        $product_region = 'US'; // string | The region where you wish to purchase the product.
        $locale = 'en_US'; // string | The locale of the request, in the form of an IETF language tag. Each ProductRegion supports one or more locales. This value should comply with java.util.Locale.
        $x_amz_user_email = 'nitrouspurchases@gmail.com'; // string | Email address of the user requesting this resource
        $shipping_postal_code = ''; // string | The postal/zip code for customer's request. This parameter doesn't hold geocode.
        $page_number = null; // int | The zero-based number of the page being requested. If not specified, a default value of 0 will be used. When passed, the value must be equal or greater than zero, and strictly less than the number of pages returned in the response.
        $page_size = null; // int | The number of items desired per page in response. If not specified, a default value of 24 will be used. Maximum items that can be fetched in single request is 24.
        $group_tag = null; // string | Group identifier to determine on behalf of which group a customer is performing this operation. This can be found in your Amazon Business account information. Only necessary if the customer account belongs to more than one group.
        $filter_ids = array('filter_ids_example'); // string[] | A list of filter ids to apply to query results.
        $quantity = 1; // int | The number of units the customer intends to purchase. This helps  Amazon to determine quantity-based discounts if an eligible offer is present. Defaults to 1.
        $inclusions_for_offers = null; // string[] | A list specifying the offer fields you want to be included in your response object. If you do not include this query parameter then all inclusions will be returned.

        try {
            $result = $apiInstance->searchOffersRequest($product_id, $product_region, $locale, $x_amz_user_email, $shipping_postal_code, $page_number, $page_size, $group_tag, $filter_ids, $quantity, $inclusions_for_offers);

            $results = json_decode(json_encode($result));
            
            return ($results);
        } catch (Exception $e) {
            echo 'Exception when calling ProductSearchV20200826Api->searchOffersRequest: ', $e->getMessage(), PHP_EOL;
        }

    }

}