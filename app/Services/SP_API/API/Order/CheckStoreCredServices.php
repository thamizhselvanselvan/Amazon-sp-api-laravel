<?php

namespace App\Services\SP_API\API\Order;

use Exception;
use Composer\IO\NullIO;
use Illuminate\Support\Carbon;
use SellingPartnerApi\Api\OrdersV0Api;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;

class CheckStoreCredServices
{
    use ConfigTrait;
    private $cred_status;
    private $seller_id;

    public function index($store_details)
    {
        $this->seller_id = $store_details['seller_id'];
        $this->cred_status = $store_details['cred_status'];

        $country_code = $store_details['country_code'];
        $store_name = $store_details['store_name'];

        $config = $this->config($this->seller_id, $country_code, Null);
        $marketplace_ids = [$this->marketplace_id($country_code)];

        $apiInstance = new OrdersV0Api($config);

        $subDays = getSystemSettingsValue('subDays', 5);
        $createdAfter = Carbon::now()->subDays($subDays)->toISOString();

        try {
            $results = $apiInstance->getOrders(
                $marketplace_ids,
                $createdAfter,
            )->getPayload();

            if ($this->cred_status == '0') {

                $slackMessage = "Store Name: $store_name
                Description: Store credential is Working";

                slack_notification('monitor', 'Store Cred Check', $slackMessage);
                $this->updateTable('1');
            }
        } catch (Exception $e) {

            if ($this->cred_status == '1' && $e->getCode() == '403') {

                $msg = $e->getMessage();

                $slackMessage = "Message: $msg
                Store Name: $store_name
                Description: Store credential is not Working";

                slack_notification('monitor', 'Store Cred Check', $slackMessage);
                $this->updateTable('0');
            }
        }
    }

    public function updateTable($update_value)
    {
        OrderSellerCredentials::where('seller_id', $this->seller_id)
            ->update(['cred_status' => $update_value]);
    }
}
