<?php

namespace App\Services\AmazonFeedApiServices;

use Exception;
use App\Models\Aws_credential;
use SellingPartnerApi\Document;
use SellingPartnerApi\FeedType;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product_Push;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\ListingsV20210801Api;
use App\Services\AmazonFeedApiServices\ProductFeed;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedSpecification;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedDocumentSpecification;

class AmazonFeedProcess
{
    use ConfigTrait;

    public function feedSubmit($feedLists, $seller_id, $product_push_id)
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

    public function process($feedLists, $merchant_id, $aws_key, $country_code, $currency_code, $marketplace_ids) {

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

    public function xml_build($feedLists, $merchant_id, $currency_code) {

        $xml_lists = '<?xml version="1.0" encoding="utf-8"?>
        <AmazonEnvelope >
            <Header>
                <DocumentVersion>1.02</DocumentVersion>
                <MerchantIdentifier>'. $merchant_id .'</MerchantIdentifier>
            </Header>
            <MessageType>Price</MessageType>';

        foreach($feedLists as $key => $feedList) {

            $xml_lists .= '
            <Message>
                <MessageID>'. $key + 1 .'</MessageID>
                <OperationType>Update</OperationType>
                <Price>
                    <SKU>'. $feedList['product_sku'] .'</SKU>
                    <StandardPrice currency="' . $currency_code . '">'. $feedList['push_price'] .'</StandardPrice>
                    <MinimumSellerAllowedPrice currency="' . $currency_code . '">'. $feedList['base_price'] .'</MinimumSellerAllowedPrice>
                    <MaximumSellerAllowedPrice currency="' . $currency_code . '">'. $feedList['ceil_price'] .'</MaximumSellerAllowedPrice>
                </Price>
            </Message>
            ';
        }

        return $xml_lists ."</AmazonEnvelope>";
    }

}
