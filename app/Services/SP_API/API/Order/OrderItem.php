<?php

namespace App\Services\SP_API\API\Order;

use Exception;
use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\Invoice;

use App\Services\BB\PushAsin;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ErrorReporting;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\OrdersV0Api;
use App\Models\order\OrderUpdateDetail;
use App\Services\SP_API\API\NewCatalog;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use App\Jobs\Seller\Seller_catalog_import_job;
use App\Models\order\OrderItemDetails;
use App\Services\Courier_Booking\B2cshipBookingServices;


class OrderItem
{
    use ConfigTrait;
    private $zoho;
    private $courier_partner;
    private $store_name;
    public function OrderItemDetails($order_id, $aws_id, $country_code, $source, $zoho, $courier_partner, $store_name)
    {
        $this->zoho = $zoho;
        $this->courier_partner = $courier_partner;
        $this->store_name = $store_name;

        $config = $this->config($aws_id, $country_code);
        $marketplace_ids = $this->marketplace_id($country_code);
        $marketplace_ids = [$marketplace_ids];

        $apiInstance = new OrdersV0Api($config);
        $this->SelectedSellerOrderItem($apiInstance, $country_code, $source, $order_id, $aws_id);
        return true;
    }

    public function SelectedSellerOrderItem($apiInstance, $awsCountryCode, $source, $order_id, $aws_id)
    {
        $data_element = array('buyerInfo');
        $next_token = NULL;

        try {
            $result_orderItems = $apiInstance->getOrderItems($order_id, $next_token, $data_element);
            $result_order_address = $apiInstance->getOrderAddress($order_id);
            $this->OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $awsCountryCode, $source, $aws_id);
        } catch (Exception $e) {

            $code =  $e->getCode();
            $msg = $e->getMessage();
            ErrorReporting::create([
                'queue_type' => "order Item",
                'identifier' => $order_id,
                'identifier_type' => "order_id",
                'source' => $awsCountryCode,
                'aws_key' => $aws_id,
                'error_code' => $code,
                'message' => $msg,
            ]);

            if ($code == '403') {
                $s_name = $this->store_name;
                (new CheckStoreCredServices())->updateTable($aws_id, '0');
                $slackMessage = "Code: 403
                Store Name: $s_name
                Country: $awsCountryCode
                Description: Store credential is not Working";
                // Log::alert($slackMessage);
                slack_notification('app360', 'Store Credential Check', $slackMessage);
            }
        }
        return true;
    }

    public function OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $awsCountryCode, $source, $aws_id)
    {
        $columns = [
            'seller_identifier',
            'status',
            'country',
            'source',
            'asin',
            'seller_sku',
            'order_item_identifier',
            'title',
            'quantity_ordered',
            'quantity_shipped',
            'product_info',
            'points_granted',
            'item_price',
            'shipping_price',
            'item_tax',
            'shipping_tax',
            'shipping_discount',
            'shipping_discount_tax',
            'promotion_discount',
            'promotion_discount_tax',
            'promotion_identifiers',
            'cod_fee',
            'cod_fee_discount',
            'is_gift',
            'condition_note',
            'condition_identifier',
            'condition_subtype_identifier',
            'scheduled_delivery_start_date',
            'scheduled_delivery_end_date',
            'price_designation',
            'tax_collection',
            'serial_number_required',
            'is_transparency',
            'ioss_number',
            'store_chain_store_identifier',
            'deemed_reseller_category',
            'buyer_info',
            'amazon_order_identifier',
            'shipping_address',
            'buyer_requested_cancel',
            'created_at',
            'updated_at'
        ];

        $order_address = '';
        $order_update_details_table = [];

        $result_order_address = (array)$result_order_address;
        foreach ($result_order_address as $result_address) {
            foreach ((array)$result_address['payload'] as $result) {
                foreach ($result as $key => $value) {

                    $detailsKey = lcfirst($key);
                    $id = substr($detailsKey, -2);
                    $ids = substr($detailsKey, -3);

                    if ($id == 'id' || $id == 'Id' || $ids == 'ids') {
                        $detailsKey = str_replace(["id", 'Id', 'ids'], "identifier", $detailsKey);
                    }

                    if (is_array($value) || is_object($value)) {

                        $order_address = json_encode($value);
                    } else {
                        $count = 1;

                        $amazon_order = $value;
                    }
                }
            }
        }

        foreach ($result_orderItems['payload']['order_items'] as $result_order) {
            foreach ((array)$result_order as $result) {

                $order_detials_arr = [];
                $missing_columns_txt = '';

                $order_detials_arr['seller_identifier']         = $aws_id;
                $order_detials_arr['status']                    = 0;
                $order_detials_arr['country']                   = $awsCountryCode;
                $order_detials_arr['source']                    = $source;
                $order_detials_arr['amazon_order_identifier']   = $order_id;

                foreach ($result as $key => $value) {

                    $detailsKey = trim(lcfirst($key));
                    $id = substr($detailsKey, -2);
                    $ids = substr($detailsKey, -3);

                    if ($id == 'id' || $id == 'Id' || $ids == 'ids') {
                        $detailsKey = str_replace(["id", 'Id', 'ids'], "identifier", $detailsKey);
                    }

                    if (array_search($detailsKey, $columns)) {

                        if (is_array($value) || is_object($value)) {
                            $order_detials_arr[$detailsKey] = json_encode($value);
                        } else {
                            $order_detials_arr[$detailsKey] = $value;
                        }

                        if ($detailsKey == 'asin') {
                            $asin = $value;
                        }

                        if ($detailsKey == 'order_item_identifier') {
                            $order_update_details_table[] =  [
                                'store_id' => $aws_id,
                                'amazon_order_id' => $order_id,
                                'order_item_id' => $value,
                                'courier_name' => $this->courier_partner,
                                'order_status' => 'unshipped'
                            ];
                        }
                    } else {
                        //send slack notification for missing columns
                        $missing_columns_txt .=  $detailsKey . ', ';
                    }
                }

                $order_detials_arr['shipping_address'] = $order_address;

                OrderItemDetails::upsert($order_detials_arr, 'order_item_identifier_UNIQUE', []);

                if ($this->zoho == 1) {
                    OrderUpdateDetail::upsert(
                        $order_update_details_table,
                        ['amzn_ord_item_id_unique'],
                        ['store_id', 'amazon_order_id', 'order_item_id']
                    );
                }

                $this->OrderItemColumnsCheck($missing_columns_txt, $order_id, $aws_id);
                $order_update_details_table = [];

                // Check if ASIN is in source's catalog table. if not then auto add and make sp api request
                (new PushAsin())->checkAsinAvailability($asin, $source, $aws_id, 'Order Item Missing Asin Catalog Import');
            }
        }

        DB::connection('order')
            ->update("UPDATE orders SET order_item = '1' where amazon_order_identifier = '$order_id'");

        return true;
    }

    public function OrderItemColumnsCheck($missing_columns_txt, $order_id, $store_id)
    {
        if ($missing_columns_txt) {

            $store_name = $this->store_name;
            $column_name = substr($missing_columns_txt, 0, -2);
            $slackMessage = "Message: New Columns Detected
            Store Id: $store_id
            Store Name: $store_name
            Order Id: $order_id
            New Columns: $column_name";

            // Log::alert($slackMessage);
            // slack_notification('app360', 'Order Item Details Import', $slackMessage);
        }
        return true;
    }
}
