<?php

namespace App\Services\SP_API\API\AmazonOrderFeed;

use App\Models\Aws_credential;
use App\Models\order\Order;
use App\Models\order\OrderItemDetails;
use App\Models\order\OrderUpdateDetail;
use Exception;

use Carbon\Carbon;
use SellingPartnerApi\Document;
use SellingPartnerApi\FeedType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\Config\ConfigTrait;
use JeroenNoten\LaravelAdminLte\View\Components\Widget\Card;
use SellingPartnerApi\Model\FeedsV20210630 as Feeds;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedSpecification;
use SellingPartnerApi\Model\FeedsV20210630\CreateFeedDocumentSpecification;

class FeedOrderDetailsApp360
{
    use ConfigTrait;

    public function FeedOrderTrackingNo($store_id, $amazon_order_id, $amazon_order_item_id, $courier_name, $courier_awb)
    {
        $data = [];
        $merchant = Aws_credential::where('seller_id', $store_id)->get();
        $merchant_id = $merchant[0]->merchant_id;

        $order_qty = OrderItemDetails::where(
            [
                'amazon_order_identifier' => $amazon_order_id,
                'order_item_identifier' => $amazon_order_item_id
            ]
        )
            ->get(['quantity_ordered', 'country']);

        $quantity = isset($order_qty[0]->quantity_ordered) ?: '1';
        $country_code = $order_qty[0]->country;

        $marketplace_ids = $this->marketplace_id($country_code);

        Log::alert('Merchant_id ->' . $merchant_id);
        Log::alert('quantity ->' . $quantity);
        Log::alert('country_code ->' . $country_code);
        Log::alert('marketplace_ids ->' . $marketplace_ids);

        $config = $this->config($store_id, $country_code, $token = NULL);

        $current_date = Carbon::now()->format('Y-m-d H:i:s');
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $current_date, 'UTC')
            ->setTimezone('America/Los_Angeles');
        $date_time_uat = Carbon::parse($date)->format('Y-m-d\TH:i:s\Z');

        $data = [
            'merchant_id' => $merchant_id,
            'amazon_order_id' => $amazon_order_id,
            'purchase_date' => $date_time_uat,
            'carrier_code' => $courier_name,
            'shipping_method' => 'Standard',
            'tracking_number' => $courier_awb,
            'AmazonOrderItemCode' => $amazon_order_item_id,
            'Quantity' => $quantity,
        ];

        $response = $this->feedAWBToAmz($marketplace_ids, $config, $data);
        $this->updateAWSTable($response, $amazon_order_id, $amazon_order_item_id);
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
            Log::channel('slack')->error('Exception when calling FeedAPI->createFeed' . $e->getMessage());
            // Log::warning('Exception when calling FeedAPI->createFeed: ' . $e->getMessage());
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

    public function updateAWSTable($response, $amazon_order_id, $amazon_order_item_id)
    {
        $response = json_decode(json_encode($response));
        $feed_id = $response->feedId;

        Log::info("${amazon_order_id} updated on amazon: ${feed_id}");
        $table_update_string = 'updated on amazon: ' . $feed_id;

        OrderUpdateDetail::where([
            ['amazon_order_id', $amazon_order_id],
            ['order_item_id', $amazon_order_item_id]
        ])->update(['order_status' => $table_update_string]);

        return true;
    }
}