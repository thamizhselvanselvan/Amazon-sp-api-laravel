<?php

use SellingPartnerApi\Document;
use SellingPartnerApi\Endpoint;
use SellingPartnerApi\FeedType;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedSpecification;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedDocumentSpecification;


Route::get("aws_price_update", function() {

    $config = new Configuration([
        "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
        "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
        "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
        "awsSecretAccessKey" => "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",
        "roleArn" => "arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role",
        "lwaRefreshToken" => "Atzr|IwEBIG3zt3kKghE3Bl56OEGAxxeodmEzfaMAnMl0PivBlfumR8224Adu9lb33DKLEvHD6OBwdIBkaVlIZ5L2axypPm-LLuKPabvUCmRZ6F6C8KZKBJYS2u1sJVqzMxxoFSs6DTFLMxx8WBVXY395aKUzK3plz3-ttDN-YUGjiKR9-kFhLek1ZdjxwTQkvUdWdfpuDtcnW0veAPS0JUHVwTN39hpwJtPXm98XwD-wEe16n9qoWoak-UvtuML8irbdUdATSA4FLSX08H2V7SFAjdktXEW13v6gBs3xfCYn_w9Y4H29K5i5_vkQyiqj0j1FMK0nmtU",
        "endpoint" => Endpoint::EU
    ]);

    $merchant_id = 'A2DMXV6IGOPV14';
    $marketplace_ids = ['A21TJRUUN4KGV'];

    $apiInstance = new FeedsApi($config);
    $feedType = FeedType::POST_PRODUCT_PRICING_DATA;

        try {
            $createFeedDocSpec  = new CreateFeedDocumentSpecification(['content_type' => $feedType['contentType']]);
            $feedDocumentInfo = $apiInstance->createFeedDocument($createFeedDocSpec);

            $feedDocumentId = $feedDocumentInfo->getFeedDocumentId();

            $feedContents = '<?xml version="1.0" encoding="utf-8"?>
            <AmazonEnvelope >
                <Header>
                    <DocumentVersion>1.02</DocumentVersion>
                    <MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
                </Header>
                <MessageType>Price</MessageType>
                <Message>
                    <MessageID>1</MessageID>
                    <OperationType>Update</OperationType>
                    <Price>
                        <SKU>NS_150500683X</SKU>
                        <MinimumSellerAllowedPrice currency="INR">3000</MinimumSellerAllowedPrice>
                        <StandardPrice currency="INR">3113</StandardPrice>
                        <MaximumSellerAllowedPrice currency="INR">5269</MaximumSellerAllowedPrice>
                    </Price>
                </Message>
            </AmazonEnvelope>';

            $docToUpload = new Document($feedDocumentInfo, $feedType);
            $docToUpload->upload($feedContents);

            $body = new CreateFeedSpecification();
            $body->setFeedType('POST_PRODUCT_PRICING_DATA');
            $body->setMarketplaceIds($marketplace_ids);
            $body->setInputFeedDocumentId($feedDocumentId);

            try {
                $result = $apiInstance->createFeed($body);

                return $result->getFeedId();

                return json_decode(json_encode($result), true);
            } catch (Exception $e) {
                echo 'Exception when calling FeedsApi->createFeed: ', $e->getMessage(), PHP_EOL;
            }

        } catch (Exception $e) {

            echo "<pre>";
            print_r($e->getMessage());
            echo "</pre>";

        }

    
});


Route::get("get_feed", function() {

    $config = new Configuration([
        "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
        "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
        "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
        "awsSecretAccessKey" => "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",
        "roleArn" => "arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role",
        "lwaRefreshToken" => "Atzr|IwEBIG3zt3kKghE3Bl56OEGAxxeodmEzfaMAnMl0PivBlfumR8224Adu9lb33DKLEvHD6OBwdIBkaVlIZ5L2axypPm-LLuKPabvUCmRZ6F6C8KZKBJYS2u1sJVqzMxxoFSs6DTFLMxx8WBVXY395aKUzK3plz3-ttDN-YUGjiKR9-kFhLek1ZdjxwTQkvUdWdfpuDtcnW0veAPS0JUHVwTN39hpwJtPXm98XwD-wEe16n9qoWoak-UvtuML8irbdUdATSA4FLSX08H2V7SFAjdktXEW13v6gBs3xfCYn_w9Y4H29K5i5_vkQyiqj0j1FMK0nmtU",
        "endpoint" => Endpoint::EU
    ]);

    $merchant_id = 'A2DMXV6IGOPV14';
    $marketplace_ids = ['A21TJRUUN4KGV'];

    $apiInstance = new SellingPartnerApi\Api\FeedsV20210630Api($config);
   // $feed_id = '133375019405'; // string | The identifier for the feed. This identifier is unique only in combination with a seller ID.
//    / $feed_id = '133377019405'; // string | The identifier for the feed. This identifier is unique only in combination with a seller ID.
    $feed_id = '133380019405'; // string | The identifier for the feed. This identifier is unique only in combination with a seller ID.

    try {
        $result = $apiInstance->getFeed($feed_id);
        echo "<pre>";
        print_r($result);
    } catch (Exception $e) {
        echo 'Exception when calling FeedsV20210630Api->getFeed: ', $e->getMessage(), PHP_EOL;
    }

});


Route::get('get_feed_document', function() {

    // See README for more information on the Configuration object's options
    $config = new Configuration([
        "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
        "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
        "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
        "awsSecretAccessKey" => "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",
        "roleArn" => "arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role",
        "lwaRefreshToken" => "Atzr|IwEBIG3zt3kKghE3Bl56OEGAxxeodmEzfaMAnMl0PivBlfumR8224Adu9lb33DKLEvHD6OBwdIBkaVlIZ5L2axypPm-LLuKPabvUCmRZ6F6C8KZKBJYS2u1sJVqzMxxoFSs6DTFLMxx8WBVXY395aKUzK3plz3-ttDN-YUGjiKR9-kFhLek1ZdjxwTQkvUdWdfpuDtcnW0veAPS0JUHVwTN39hpwJtPXm98XwD-wEe16n9qoWoak-UvtuML8irbdUdATSA4FLSX08H2V7SFAjdktXEW13v6gBs3xfCYn_w9Y4H29K5i5_vkQyiqj0j1FMK0nmtU",
        "endpoint" => Endpoint::EU
    ]);

    $apiInstance = new SellingPartnerApi\Api\FeedsV20210630Api($config);
    //$feed_document_id = 'amzn1.tortuga.4.eu.8442a27a-f1b5-4604-8b4a-732c0dbcf0cd.T1URPFGA95788P'; // string | The identifier of the feed document.
    $feed_document_id = 'amzn1.tortuga.4.eu.8442a27a-f1b5-4604-8b4a-732c0dbcf0cd.T1URPFGA95788P'; // string | The identifier of the feed document.

    try {
        $result = $apiInstance->getFeedDocument($feed_document_id);
        $data = json_decode(json_encode($result), true);

        echo "<pre>";
        print_r($result);

        if (array_key_exists('url', $data)) {

            $httpResponse = file_get_contents($data['url']);

            if (array_key_exists('compression_algorithm', $data)) {

                $httpResponse = gzdecode($httpResponse);
            }

            Storage::put('/aws/attempt1.txt', $httpResponse);

        }


       
    } catch (Exception $e) {
        echo 'Exception when calling FeedsV20210630Api->getFeedDocument: ', $e->getMessage(), PHP_EOL;
    }

});