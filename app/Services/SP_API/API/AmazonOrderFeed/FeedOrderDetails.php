<?php

namespace App\Services\SP_API\API\AmazonOrderFeed;

use Exception;

use Carbon\Carbon;
use SellingPartnerApi\Document;
use SellingPartnerApi\FeedType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Model\FeedsV20210630 as Feeds;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedSpecification;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedDocumentSpecification;

class FeedOrderDetails
{
    use ConfigTrait;

    public function FeedOrderTrackingNo()
    {
        $country_code = 'IN';
        $carrier_name = 'B2CShip';
        $data = [];
        $amazon_order_id = '';
        $store_array = [
            5 => 'in_mbm',
            6 => 'nitrous'
        ];

        $merchant_array = [
            5 => 'A2700HXFTQX0S5',
            6 => 'A2DMXV6IGOPV14'
        ];

        $id_array = [
            5 => 4080,
            6 => 92490
        ];

        // $config = $this->config(6, $country_code, $token = NULL);
        // $apiInstance = new FeedsApi($config);
        // po($apiInstance->getFeed('127454019258'));
        // exit;
        // po($apiInstance->getFeedDocument('amzn1.tortuga.4.eu.aa6af08c-9f85-42e2-85e9-5d87c72f9e96.T31ALWO5K8AOQ6'));
        // exit;

        $marketplace_ids = $this->marketplace_id($country_code);

        foreach ($store_array as $store_aws_id => $store_name) {
            $config = $this->config($store_aws_id, $country_code, $token = NULL);

            $merchant_id = $merchant_array[$store_aws_id];
            $limit_id = $id_array[$store_aws_id];
            $config = $this->config($store_aws_id, $country_code, $token = NULL);

            $store_data = DB::connection('aws')
                ->select("SELECT amazon_order_id, courier_awb, order_item_id, purchase_date, quantity
                    FROM 
                        ${store_name}_amazon_order_details 
                    WHERE courier_awb <> ''
                             AND 
                        amzn_temp_order_status = 'unshipped'
                             AND
                         id > $limit_id 
                    LIMIT 1");

            if (count($store_data) > 0) {
                foreach ($store_data as $details) {

                    $date = Carbon::parse($details->purchase_date)->addSeconds(10)->format('Y-m-d\TH:i:s\Z');
                    $amazon_order_id = $details->amazon_order_id;

                    $data = [
                        'merchant_id' => $merchant_id,
                        'amazon_order_id' => $details->amazon_order_id,
                        'purchase_date' => $date,
                        'carrier_code' => $carrier_name,
                        'shipping_method' => 'Standard',
                        'tracking_number' => $details->courier_awb,
                        'AmazonOrderItemCode' => $details->order_item_id,
                        'Quantity' => $details->quantity,
                    ];
                }

                $response = $this->feedAWBToAmz($marketplace_ids, $config, $data);
                $this->updateAWSTable($response, $amazon_order_id, $store_name);
            }
        }
    }

    public function feedAWBToAmz($marketplace_ids, $config, $data)
    {
        $xml = $this->getXml($data);

        $apiInstance = new FeedsApi($config);

        $feedType = FeedType::POST_ORDER_FULFILLMENT_DATA;
        $body = new CreateFeedDocumentSpecification(['content_type' => $feedType['contentType']]);

        $feedDocument = $apiInstance->createFeedDocument($body);

        $docToUpload = new Document($feedDocument, $feedType);
        $docToUpload->upload($xml);

        $body = new CreateFeedSpecification(
            [
                'feed_type' => FeedType::POST_ORDER_FULFILLMENT_DATA['name'],
                'marketplace_ids' => [$marketplace_ids],
                'input_feed_document_id' => $feedDocument->getFeedDocumentId()
            ]
        );

        try {

            $result = $apiInstance->createFeed($body);
            return $result;
        } catch (Exception $e) {

            Log::warning('Exception when calling FeedAPI->createFeed: ' . $e->getMessage());
        }
    }

    public function getXml($data)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <AmazonEnvelope>
          <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>' . $data['merchant_id'] . '</MerchantIdentifier>
          </Header>
          <MessageType>OrderFulfillment</MessageType>
          <Message>
            <MessageID>1</MessageID>
            <OperationType>PartialUpdate</OperationType>
            <OrderFulfillment>
              <AmazonOrderID>' . $data['amazon_order_id'] . '</AmazonOrderID>
              <FulfillmentDate>' . $data['purchase_date'] . '</FulfillmentDate>
              <FulfillmentData>
                <CarrierCode>B2CShip</CarrierCode>
                <ShippingMethod>' . $data['shipping_method'] . '</ShippingMethod>
                <ShipperTrackingNumber>' . $data['tracking_number'] . '</ShipperTrackingNumber>
              </FulfillmentData>
              <Item>
                <AmazonOrderItemCode>' . $data['AmazonOrderItemCode'] . '</AmazonOrderItemCode>
                <Quantity>' . $data['Quantity'] . '</Quantity>
              </Item>
            </OrderFulfillment>
          </Message>
        </AmazonEnvelope>';

        return $xml;
    }

    public function updateAWSTable($response, $amazon_order_id, $store_name)
    {
        $response = json_decode(json_encode($response));
        $feed_id = $response->feedId;

        Log::info("${amazon_order_id} updated on amazon: ${feed_id}");
        $table_update_string = 'updated on amazon: ' . $feed_id;

        DB::connection('aws')
            ->select("UPDATE ${store_name}_amazon_order_details 
                SET 
                    amzn_temp_order_status = '$table_update_string' 
                WHERE
                     amazon_order_id = '$amazon_order_id'
            ");

        return true;
    }
}
