<?php

namespace App\Services\test;

use in;
use Exception;
use App\Models\Aws_credential;
use SellingPartnerApi\FeedType;
use SellingPartnerApi\Document;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedSpecification;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedDocumentSpecification;


class PriceFeed
{
    use ConfigTrait;
    public function price_submit($feedLists, $asin)
    {
        $aws = '';

        $seller_id = $feedLists['store_id'];

        $aws = Aws_credential::where('seller_id', $seller_id)->where('verified', 1)->with(['mws_region'])->first();
        $merchant_id = $aws->merchant_id;
        $mws_region = $aws->mws_region;
        $country_code = $mws_region->region_code;
        $currency_code = $mws_region->currency->code;
        $marketplace_id = $mws_region->marketplace_id;

        $productFeed = $this->process($feedLists, $merchant_id, $seller_id, $country_code,  $currency_code, [$marketplace_id]);
        Log::notice($productFeed);
        if (!$productFeed) {

            return false;
        }

        return $productFeed;
    }

    public function process($feedLists, $merchant_id, $aws_key, $country_code, $currency_code, $marketplace_ids)
    {

        $apiInstance = new FeedsApi($this->config($aws_key, $country_code));
        $feedType = FeedType::POST_PRODUCT_PRICING_DATA;

        try {
            $createFeedDocSpec  = new CreateFeedDocumentSpecification(['content_type' => $feedType['contentType']]);
            $feedDocumentInfo = $apiInstance->createFeedDocument($createFeedDocSpec);

            $docToUpload = new Document($feedDocumentInfo, $feedType);
            $docToUpload->upload($this->xml_build($feedLists, $merchant_id, $currency_code));

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

    public function xml_build($feedLists, $merchant_id, $currency_code)
    {

        $xml_lists = '<?xml version="1.0" encoding="utf-8"?>
        <AmazonEnvelope >
            <Header>
                <DocumentVersion>1.02</DocumentVersion>
                <MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
            </Header>
            <MessageType>Price</MessageType>';


        $xml_lists .= '
            <Message>
                <MessageID>' . 100001 . '</MessageID>
                <OperationType>Update</OperationType>
                <Price>
                    <SKU>' . $feedLists['sku'] . '</SKU>
                    <StandardPrice currency="' . $currency_code . '">' . $feedLists['push_price'] . '</StandardPrice>
                    <MinimumSellerAllowedPrice currency="' . $currency_code . '">' . $feedLists['base_price'] . '</MinimumSellerAllowedPrice>
                    <MaximumSellerAllowedPrice currency="' . $currency_code . '">' . $feedLists['ceil_price'] . '</MaximumSellerAllowedPrice>
                </Price>
            </Message>';

        return $xml_lists . "</AmazonEnvelope>";
    }
}
