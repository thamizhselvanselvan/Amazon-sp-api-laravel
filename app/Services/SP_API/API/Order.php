<?php

namespace App\Services\SP_API\Order;

use Exception;
use SellingPartnerApi\Endpoint;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use App\Services\SP_API\Config\ConfigTrait;


class Order
{

    //use ConfigTrait;

    public function getOrders($aws_key, $country_code)
    {

        $config = new Configuration([
            "lwaClientId" => "<LWA client ID>",
            "lwaClientSecret" => "<LWA client secret>",
            "lwaRefreshToken" => "<LWA refresh token>",
            "awsAccessKeyId" => "<AWS access key ID>",
            "awsSecretAccessKey" => "<AWS secret access key>",
            "endpoint" => Endpoint::NA  // or another endpoint from lib/Endpoints.php
        ]);
        

        $apiInstance = new OrdersApi($config);
        $order_id = 'order_id_example'; // string | An Amazon-defined order identifier, in 3-7-7 format.
        $data_elements = array('data_elements_example'); // string[] | An array of restricted order data elements to retrieve (valid array elements are \"buyerInfo\" and \"shippingAddress\")

        try {
            $result = $apiInstance->getOrder($order_id, $data_elements);
            print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling OrdersApi->getOrder: ', $e->getMessage(), PHP_EOL;
        }
    }
}
