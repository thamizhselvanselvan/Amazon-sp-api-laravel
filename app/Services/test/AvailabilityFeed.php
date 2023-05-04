<?php

namespace App\Services\test;

use Exception;
use App\Models\Aws_credential;
use SellingPartnerApi\Document;
use SellingPartnerApi\FeedType;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product_Push;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\Buybox_stores\Product_push_ae;
use App\Models\Buybox_stores\Product_push_in;
use SellingPartnerApi\Api\ListingsV20210801Api;
use App\Models\Buybox_stores\Product_availability_ae;
use App\Models\Buybox_stores\Product_availability_in;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use SellingPartnerApi\Model\ListingsV20210801\PatchOperation;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedSpecification;
use SellingPartnerApi\Model\ListingsV20210801\FulfillmentAvailability;
use SellingPartnerApi\Model\ListingsV20210801\ListingsItemPatchRequest;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedDocumentSpecification;

class AvailabilityFeed
{
    use ConfigTrait;

    public function availability_feed($feedLists, $seller_id, $regionCode, $asin, $availability)
    {
        $aws = '';
        $aws = Aws_credential::where('seller_id', $seller_id)->where('verified', 1)->with(['mws_region'])->first();
        $merchant_id = $aws->merchant_id;
        $mws_region = $aws->mws_region;
        $country_code = $mws_region->region_code;
        $marketplace_id = $mws_region->marketplace_id;

        $productFeed = $this->process($feedLists, $merchant_id, $seller_id, $country_code, [$marketplace_id]);

        if (!$productFeed) {
            return ['failed' => true];
        }
        return $productFeed;
    }

    public function process($feedLists, $merchant_id, $aws_key, $country_code, $marketplace_ids)
    {

        $apiInstance = new FeedsApi($this->config($aws_key, $country_code));
        $feedType = FeedType::POST_INVENTORY_AVAILABILITY_DATA;

        try {
            $createFeedDocSpec  = new CreateFeedDocumentSpecification(['content_type' => $feedType['contentType']]);
            $feedDocumentInfo = $apiInstance->createFeedDocument($createFeedDocSpec);

            $docToUpload = new Document($feedDocumentInfo, $feedType);
            $docToUpload->upload($this->xml_availability($feedLists, $merchant_id));

            $body = new CreateFeedSpecification();
            $body->setFeedType($feedType['name']);
            $body->setMarketplaceIds($marketplace_ids);
            $body->setInputFeedDocumentId($feedDocumentInfo->getFeedDocumentId());

            $result = $apiInstance->createFeed($body);

            return $result->getFeedId();
        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    public function xml_availability($feedLists, $merchant_id)
    {

        $messages = '';
        $counter = 1;

        foreach ($feedLists as $feedlist) {

            $latency = (isset($feedLists['latency'])) ? '<FulfillmentLatency >' . $feedLists['latency'] . '</FulfillmentLatency>' : '';

            $messages .= '
                <Message>
                    <MessageID>' . $counter . '</MessageID>
                    <OperationType>Update</OperationType>
                    <Inventory>
                        <SKU>' . $feedlist['product_sku'] . '</SKU>
                        <Available >' . $feedlist['available'] . ' </Available>
                            <FulfillmentLatency>0</FulfillmentLatency>
                    </Inventory>
                </Message>';

            $counter++;
        }

        $feed = '<?xml version="1.0" encoding="utf-8"?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
                <Header>
                    <DocumentVersion>1.01</DocumentVersion>
                    <MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
                </Header>
                <MessageType>Inventory</MessageType>
                ' . $messages . '
            </AmazonEnvelope>';

        return $feed;
    }

    public function listing($sku, $aws_key, $country_code)
    {


        $apiInstance = new ListingsV20210801Api($this->config($aws_key, $country_code));
        //$seller_id = $merchant_id; // string | A selling partner identifier, such as a merchant account or vendor code.
        // $sku = 'NS_B07XYX72M5'; // string | A selling partner provided identifier for an Amazon listing.
        $marketplace_ids = ['A21TJRUUN4KGV']; // string[] | A comma-delimited list of Amazon marketplace identifiers for the request.


        // $body = new ListingsItemPatchRequest();
        // $patches = new PatchOperation();
        // $fulfillmentAvailability = new FulfillmentAvailability();

        // $body->setProductType('PRODUCT');
        // $fulfillmentAvailability->setFulfillmentChannelCode('DEFAULT');
        // $fulfillmentAvailability->setQuantity(1);

        // $patches->setOp('replace');
        // $patches->setPath('/attributes/fulfillment_availability');
        // $patches->setValue([$fulfillmentAvailability]);
        // $body->setPatches([$patches]);

        $body = [
            "productType" => "PRODUCT",
            "patches" => [
                new PatchOperation([
                    "op" => "replace",
                    "path" => "/attributes/fulfillment_availability",
                    "value" => [
                        [
                            "quantity" => 5,
                            "fulfillment_channel_code" => "DEFAULT"
                        ]
                    ]
                ])

            ]
        ];


        try {
            $result = $apiInstance->patchListingsItem('A2DMXV6IGOPV14', $sku, $marketplace_ids, $body);

            echo "<pre>";
            print_r($result);

            return $result;
        } catch (Exception $e) {
            echo 'Exception when calling ListingsV20210801Api->patchListingsItem: ', $e->getMessage(), PHP_EOL;
        }
    }

    public function getListing($sku, $aws_key, $country_code)
    {


        // $apiInstance = new ListingsV20210801Api($this->config($aws_key, $country_code));
        // $seller_id = $merchant_id; // string | A selling partner identifier, such as a merchant account or vendor code.
        // $sku = 'NS_B0011457OS'; // string | A selling partner provided identifier for an Amazon listing.
        // $marketplace_ids = ['A21TJRUUN4KGV']; // string[] | A comma-delimited list of Amazon marketplace identifiers for the request.
        // $body = new ListingsItemPatchRequest([
        //     "productType" => "PRODUCT",
        //     "patches" => [
        //         [
        //             "op" => "replace",
        //             "operation_type" => "PARTIAL_UPDATE",
        //             "path" => "/attributes/fulfillment_availability",
        //             "value" => [
        //                 [
        //                     "fulfillment_channel_code" => "DEFAULT",
        //                     "quantity" => 25
        //                 ]
        //             ]
        //         ]
        //     ]
        // ]); // \SellingPartnerApi\Model\ListingsV20210801\ListingsItemPatchRequest | The request body schema for the patchListingsItem operation.

        //$issue_locale = 'en_US'; // string | A locale for localization of issues. When not provided, the default language code of the first marketplace is used. Examples: \"en_US\", \"fr_CA\", \"fr_FR\". Localized messages default to \"en_US\" when a localization is not available in the specified locale.
        //, $issue_locale



        // try {
        //     $result = $apiInstance->patchListingsItem('A2DMXV6IGOPV14', $sku, $marketplace_ids, $body);
        //     print_r($result);
        // } catch (Exception $e) {
        //     echo 'Exception when calling ListingsV20210801Api->patchListingsItem: ', $e->getMessage(), PHP_EOL;
        // }


        $apiInstance = new ListingsV20210801Api($this->config($aws_key, $country_code));
        $seller_id = 'seller_id_example'; // string | A selling partner identifier, such as a merchant account or vendor code.
        //  $sku = 'sku_example'; // string | A selling partner provided identifier for an Amazon listing.
        $marketplace_ids = ['A21TJRUUN4KGV']; // string[] | A comma-delimited list of Amazon marketplace identifiers for the request.
        $issue_locale = 'en_US'; // string | A locale for localization of issues. When not provided, the default language code of the first marketplace is used. Examples: \"en_US\", \"fr_CA\", \"fr_FR\". Localized messages default to \"en_US\" when a localization is not available in the specified locale.
        $included_data = ['summaries']; // string[] | A comma-delimited list of data sets to include in the response. Default: summaries.

        try {
            $result = $apiInstance->getListingsItem('A2DMXV6IGOPV14', $sku, $marketplace_ids, $issue_locale, $included_data);
            return $result;
            print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling ListingsV20210801Api->getListingsItem: ', $e->getMessage(), PHP_EOL;
        }
    }
}
