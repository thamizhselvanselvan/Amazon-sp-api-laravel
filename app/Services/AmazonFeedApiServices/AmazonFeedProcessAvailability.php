<?php

namespace App\Services\AmazonFeedApiServices;

use Exception;
use App\Models\Aws_credential;
use SellingPartnerApi\Document;
use SellingPartnerApi\FeedType;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product_Push;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedSpecification;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedDocumentSpecification;

class AmazonFeedProcessAvailability
{
    use ConfigTrait;

    public function availabilitySubmit($feedLists, $seller_id, $product_push_id)
    {
        $aws = '';
        $aws = Aws_credential::where('seller_id', $seller_id)->where('verified', 1)->with(['mws_region'])->first();
        $merchant_id = $aws->merchant_id;
        $mws_region = $aws->mws_region;
        $country_code = $mws_region->region_code;
        $currency_code = $mws_region->currency->code;
        $marketplace_id = $mws_region->marketplace_id;

        $productFeed = $this->process($feedLists, $merchant_id, $seller_id, $country_code,  $currency_code, [$marketplace_id]);

        if (!$productFeed) {
            return false;
        }

        $feedId = $productFeed;
        Product_Push::where("id", $product_push_id)->update(['feedback_price_id' => $feedId['feedId']]);

        return true;
    }
    public function process($feedLists, $merchant_id, $aws_key, $country_code, $currency_code, $marketplace_ids)
    {

        $apiInstance = new FeedsApi($this->config($aws_key, $country_code));
        $feedType = FeedType::POST_INVENTORY_AVAILABILITY_DATA;

        try {
            $createFeedDocSpec  = new CreateFeedDocumentSpecification(['content_type' => $feedType['contentType']]);
            $feedDocumentInfo = $apiInstance->createFeedDocument($createFeedDocSpec);

            $docToUpload = new Document($feedDocumentInfo, $feedType);
            $docToUpload->upload($this->xml_availability($feedLists, $merchant_id, $currency_code));

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

    public function xml_availability($feedLists, $merchant_id, $currency_code)
    {

        $messages = '';
        $counter = 1;
        foreach ($feedLists as $feedlist) {
            $latency = (isset($feedLists['latency'])) ? '<FulfillmentLatency >' . $feedLists['latency'] . '</FulfillmentLatency>' : '';
            $messages .= '
            <Message>
                <MessageID>' . $counter . '</MessageID>
                <Inventory>
                    <SKU>' . $feedlist['product_sku'] . '</SKU>
                    <Available >' . $feedlist['available'] . ' </Available>
                    ' . $latency . '
                    <Quantity>25</Quantity>
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
        </AmazonEnvelope>
    ';
        return $feed;
    }
}
