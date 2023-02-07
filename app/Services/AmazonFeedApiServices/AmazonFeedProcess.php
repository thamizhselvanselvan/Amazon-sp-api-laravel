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

    public function feedSubmit($feedLists, $seller_id, $product_push_id, $availability)
    {

        $aws = '';
        $aws_key = '';

        $aws = Aws_credential::where('seller_id', $seller_id)->where('api_type', 1)->with(['mws_region'])->first();
        $aws_key = $aws->id;
        $merchant_id = $aws->merchant_id;
        $mws_region = $aws->mws_region;
        $country_code = $mws_region->region_code;
        $currency_code = $mws_region->currency->code;
        $marketplace_id = $mws_region->marketplace_id;

        $productFeed = $this->createFeedDocument($aws_key, $country_code, $feedLists, $merchant_id, $currency_code, [$marketplace_id], $availability);
        // Log::critical($productFeed);

        if (!$productFeed) {

            // event(new ProductImportCompleted($seller_id, "Your price push has failed check with admin"));
           // throw new Exception('Feed submit showing error 1');

           return false;
        }

        return $productFeed;  

        $feedId = $productFeed;

        // if(!array_key_exists("feedId", $feedId)) {

        //     return false;
        // }

              
        
        //Product_Push::where("id", $product_push_id)->update(['feedback_price_id' => $feedId['feedId']]);
        //event(new ProductImportCompleted($seller_id, "Your price push has submitted successfully"));
    }

    public function createFeedDocument($aws_key, $country_code, $feedLists, $merchant_id, $currency_code, $marketplace_ids, $available = false)
    {

        $apiInstance = new FeedsApi($this->config($aws_key, $country_code));
        $feedType = FeedType::POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA;

        try {
            $createFeedDocSpec  = new CreateFeedDocumentSpecification(['content_type' => $feedType['contentType']]);
            $feedDocumentInfo = $apiInstance->createFeedDocument($createFeedDocSpec);

            //echo $feedDocumentInfo;

            $feedDocumentId = $feedDocumentInfo->getFeedDocumentId();

            $feedContents = $this->xml($feedLists, $merchant_id, $currency_code);

            $docToUpload = new Document($feedDocumentInfo, $feedType);
            $docToUpload->upload($feedContents);

            Log::notice("Document ID" ." - ". $feedDocumentId ." - ". json_encode($feedLists));

            // $body = new CreateFeedSpecification(
            //     [
            //         'feed_type' => FeedType::POST_PRODUCT_PRICING_DATA['name'],
            //         'marketplace_ids' => [$this->marketplace_id($country_code)],
            //         'input_feed_document_id' => $feedDocumentInfo->getFeedDocumentId()
            //     ]
            // );
    
            // try {
            //     $result = $apiInstance->createFeed($body);

            //     Log::info($result);

            //     return $result;
            // } catch (Exception $e) {
            //     Log::channel('slack')->error('Exception when calling FeedAPI->createFeed' . $e->getMessage());
            // }


            $FEED = $this->createFeed($apiInstance, $marketplace_ids, $feedDocumentId, $available);

            Log::info($FEED);
                
            return $FEED;

            //return json_decode(json_encode($result), true);
        } catch (Exception $e) {

            echo "<pre>";
            print_r($e->getMessage());
            echo "</pre>";

          //  echo 'Exception when calling FeedsApi->createFeedDocument: ', $e->getMessage(), PHP_EOL;
        }
    }


    public function xml($feedLists, $merchant_id, $currency_code)
    {

        $messages = '';
        $counter = 1;

        foreach ($feedLists as $feedlist) {

            $messages .= '
                <Message>
                    <MessageID>' . $counter . '</MessageID>
                    <Price>
                        <SKU>' . $feedlist['product_sku'] . '</SKU>
                        <StandardPrice currency="INR">' . $feedlist['push_price'] . '</StandardPrice>
                    </Price>
                </Message>';

            $counter++;
        }

        $feed = '<?xml version="1.0" encoding="utf-8"?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
                <Header>
                    <DocumentVersion>1.01</DocumentVersion>
                    <MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
                </Header>
                <MessageType>Price</MessageType>
                ' . $messages . '
            </AmazonEnvelope>';

        return $feed;
    }

    public function createFeed($apiInstance, $marketplace_ids, $feedDocumentId)
    {

        //$apiInstance = new FeedsApi($this->config($aws_key, $country_code));
        $body = new CreateFeedSpecification();
        $body->setFeedType('POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA');
        $body->setMarketplaceIds($marketplace_ids);
        $body->setInputFeedDocumentId($feedDocumentId);

        try {
            $result = $apiInstance->createFeed($body);
            return $result->getFeedId();
            Log::notice($marketplace_ids);
            Log::notice("Create Feed ID" ." - ". $feedDocumentId ." - ". json_encode($result));

            return json_decode(json_encode($result), true);
        } catch (Exception $e) {
            echo 'Exception when calling FeedsApi->createFeed: ', $e->getMessage(), PHP_EOL;
        }
    }


    public function index($aws_key, $seller_id, $sku, $marketplace_ids, $country_code) {
        

        $apiInstance = new ListingsV20210801Api($this->config($aws_key, $country_code));
        $result = $apiInstance->getListingsItem($seller_id, $sku, $marketplace_ids, '', ['offers', 'fulfillmentAvailability']);

        echo "<pre>";
        print_r($result);
    }

    public function product_update($aws_key, $seller_id, $sku, $marketplace_ids, $country_code) {

        $apiInstance = new ListingsV20210801Api($this->config($aws_key, $country_code));
        //$result = $apiInstance->getListingsItem($seller_id, $sku, $marketplace_ids, '', ['offers']);

        // $body = new \SellingPartnerApi\Model\ListingsV20210801\ListingsItemPatchRequest();
        // $body->setProductType("Product");
        // $body->setProductType("Product");

        $body = "
        {
            'productType': 'PRODUCT',
            'requirements': 'LISTING_OFFER_ONLY',
        }
        ";

        try {
            $result = $apiInstance->patchListingsItem($seller_id, $sku, $marketplace_ids, $body);
            print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling ListingsV20210801Api->patchListingsItem: ', $e->getMessage(), PHP_EOL;
        }

        echo "<pre>";
        print_r($result);
    }
}
