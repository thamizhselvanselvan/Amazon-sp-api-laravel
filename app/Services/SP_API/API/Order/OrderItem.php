<?php

namespace App\Services\SP_API\API\Order;

use Exception;
use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\OrdersV0Api;
use App\Services\SP_API\API\NewCatalog;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;
use App\Jobs\Seller\Seller_catalog_import_job;
use App\Models\Admin\ErrorReporting;
use App\Models\Invoice;
use App\Models\Mws_region;
use App\Models\order\OrderUpdateDetail;

class OrderItem
{
    use ConfigTrait;
    private $zoho;
    private $courier_partner;
    public function OrderItemDetails($order_id, $aws_id, $country_code, $source, $zoho, $courier_partner)
    {
        $this->zoho = $zoho;
        $this->courier_partner = $courier_partner;

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
                'queue_type' => "order",
                'identifier' => $order_id,
                'identifier_type' => "order_id",
                'source' => $awsCountryCode,
                'aws_key' => $aws_id,
                'error_code' => $code,
                'message' => $msg,
            ]);

            //slack msg
        }
        return true;
    }

    public function OrderItemDataFormating($result_orderItems, $result_order_address, $order_id, $awsCountryCode, $source, $aws_id)
    {
        $host = config('database.connections.order.host');
        $dbname = config('database.connections.order.database');
        $port = config('database.connections.order.port');
        $username = config('database.connections.order.username');
        $password = config('database.connections.order.password');

        if (!R::testConnection('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
            R::addDatabase('order', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            R::selectDatabase('order');
        }

        $order_address = '';
        $amazon_order = '';
        $data  = [];
        $order_update_details_table = [];

        $result_order_address = (array)$result_order_address;
        foreach ($result_order_address as $result_address) {
            foreach ((array)$result_address['payload'] as $result) {
                $count = 0;
                foreach ($result as $key => $value) {

                    $detailsKey = lcfirst($key);
                    $id = substr($detailsKey, -2);
                    $ids = substr($detailsKey, -3);
                    // echo $id;
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

        $data = [];

        $class =  'catalog\AmazonCatalogImport';
        $queue_name = 'inventory';
        $queue_delay = 0;
        $asins = [];
        $asin_source = [];
        $invoice_data = [];
        $inv_adrs_arr = [
            'AddressLine1' => NULL,
            'AddressLine2' => NULL,
            'City' => NULL,
            'County' => NULL,
            'Country' => NULL,
            'CountryCode' => NULL
        ];

        $inv_req_arr = [
            'seller_sku' => 'sku',
            'title' => 'item_description',
            'quantity_ordered' => 'qty'
        ];

        $tem_price = 0;
        $catalog_table_name = 'catalognew' . strtolower($source) . 's';

        foreach ($result_orderItems['payload']['order_items'] as $result_order) {
            foreach ((array)$result_order as $result) {

                $order_detials = R::dispense('orderitemdetails');
                $order_detials->seller_identifier = $aws_id;
                $order_detials->status = '0';
                $order_detials->country = $awsCountryCode;
                $order_detials->source = $source;

                foreach ($result as $key => $value) {
                    $detailsKey = lcfirst($key);
                    $id = substr($detailsKey, -2);
                    $ids = substr($detailsKey, -3);

                    if ($id == 'id' || $id == 'Id' || $ids == 'ids') {
                        $detailsKey = str_replace(["id", 'Id', 'ids'], "identifier", $detailsKey);
                    }

                    if (is_array($value) || is_object($value)) {
                        $order_detials->{$detailsKey} = json_encode($value);
                        if ($detailsKey == 'item_price') {

                            $invoice_data['currency'] = $value->CurrencyCode;
                            $tem_price = $value->Amount;
                        }
                    } else {
                        $order_detials->{$detailsKey} = ($value);
                    }
                    if ($detailsKey == 'asin') {
                        $asin = $value;
                    }

                    if (array_key_exists($detailsKey, $inv_req_arr)) {
                        $invoice_data[$inv_req_arr[$detailsKey]] = $value;
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
                }

                $order_address_arr = json_decode($order_address, true);
                if ($order_address_arr) {
                    if (array_key_exists('Name', $order_address_arr)) {
                        $invoice_data['bill_to_name'] = $order_address_arr['Name'];
                        $invoice_data['ship_to_name'] = $order_address_arr['Name'];
                    }
                }

                $tem_add = '';
                foreach ($inv_adrs_arr as $key => $add_value) {
                    if ($order_address_arr) {
                        if (array_key_exists($key, $order_address_arr)) {
                            $tem_add .= $order_address_arr[$key] . ', ';
                        }
                    }
                }

                $invoice_data['bill_to_add'] = substr_replace($tem_add, "", -2);
                $invoice_data['ship_to_add'] = substr_replace($tem_add, "", -2);
                $invoice_data['amazon_order_identifier'] = $order_id;

                $order_detials->amazon_order_identifier = $order_id;
                $order_detials->shipping_address = $order_address;
                $order_detials->created_at = now();
                $order_detials->updated_at = now();
                R::store($order_detials);

                if ($this->zoho == 1) {
                    OrderUpdateDetail::upsert($order_update_details_table, ['amzn_ord_item_id_unique'], ['store_id', 'amazon_order_id', 'order_item_id']);
                }

                $order_update_details_table = [];

                $qty = $invoice_data['qty'] > 0 ? $invoice_data['qty'] : 1;
                $invoice_data['product_price'] = (float)($tem_price / $qty);

                // /Check if ASIN is in source catalog table. if not then auto add and make product sp api request

                try {
                    $asins = DB::connection('catalog')->select("SELECT asin FROM $catalog_table_name where asin = '$asin' ");

                    $country_code_up = strtoupper($source);
                    $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code_up)->get()->toArray();

                    $aws_id = $mws_regions[0]['aws_verified'][0]['id'];

                    if (count($asins) <= 0) {

                        $asin_source[] = [
                            'asin' => $asin,
                            'seller_id' => $aws_id,
                            'source' => $source,
                            'id'    =>  $aws_id,
                        ];

                        (new NewCatalog())->Catalog($asin_source);
                    }
                } catch (Exception $e) {

                    $getMessage = $e->getMessage();
                    $getCode = $e->getCode();
                    $getFile = $e->getFile();
                    $getLine = $e->getLine();

                    $slackMessage = "Message: $getMessage 
                    Code: $getCode,
                    File: $getFile,
                    Line: $getLine";
                    Log::debug($slackMessage);
                    // slack_notification('app360', 'Order Item Details', $slackMessage);
                }
            }
        }

        DB::connection('order')
            ->update("UPDATE orders SET order_item = '1' where amazon_order_identifier = '$order_id'");

        return true;
    }
}
