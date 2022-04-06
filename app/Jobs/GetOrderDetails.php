<?php

namespace App\Jobs;

use Exception;
use RedBeanPHP\R as R;
use Illuminate\Bus\Queueable;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class GetOrderDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $payload;
    private $host;
    private $dbname;
    private $port;
    private $username;
    private $password;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = 'Atzr|IwEBIG3zt3kKghE3Bl56OEGAxxeodmEzfaMAnMl0PivBlfumR8224Adu9lb33DKLEvHD6OBwdIBkaVlIZ5L2axypPm-LLuKPabvUCmRZ6F6C8KZKBJYS2u1sJVqzMxxoFSs6DTFLMxx8WBVXY395aKUzK3plz3-ttDN-YUGjiKR9-kFhLek1ZdjxwTQkvUdWdfpuDtcnW0veAPS0JUHVwTN39hpwJtPXm98XwD-wEe16n9qoWoak-UvtuML8irbdUdATSA4FLSX08H2V7SFAjdktXEW13v6gBs3xfCYn_w9Y4H29K5i5_vkQyiqj0j1FMK0nmtU';
        $config = new Configuration([
            "lwaClientId" => config('app.aws_sp_api_client_id'),
            "lwaClientSecret" => config('app.aws_sp_api_client_secret'),
            "awsAccessKeyId" => config('app.aws_sp_api_access_key_id'),
            "awsSecretAccessKey" => config('app.aws_sp_api_access_secret_id'),
            "lwaRefreshToken" => $token,
            "roleArn" => config('app.aws_sp_api_role_arn'),
            "endpoint" => Endpoint::EU,
        ]);
        $this->host = config('database.connections.web.host');
        $this->dbname = config('database.connections.web.database');
        $this->port = config('database.connections.web.port');
        $this->username = config('database.connections.web.username');
        $this->password = config('database.connections.web.password');

        $order_id = $this->payload['order_id'];
        $seller_id = $this->payload['seller_id'];
        $host = $this->host;
        $dbname = $this->dbname;
        $port = $this->port;
        $username = $this->username;
        $password = $this->password;
        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        
        $this->getOrderapi($config, $order_id, $seller_id);

        $this->getOrderItemsApi($config, $order_id, $seller_id);
    }

    public function getOrderapi($config, $order_id, $seller_id)
    {
        

        $apiInstance = new OrdersApi($config);
        $data_elements = ['buyerInfo', 'shippingAddress']; // string[] | An array of restricted order data elements to retrieve (valid array elements are \"buyerInfo\" and \"shippingAddress\")
        try {
            $results = $apiInstance->getOrder($order_id, $data_elements)->getPayload();

            $results = json_decode(json_encode($results));
            $order_details = R::dispense('orderdetails');
            foreach ($results as $resultkey => $result) {

                $search = 'Id';
                $replaceVal = 'Identifier';
                $resultkey = lcfirst($resultkey);
                $order_details->seller_identifier = $seller_id;

                if (substr($resultkey, -2) == 'Id') {

                    $resultkey = str_replace($search, $replaceVal, $resultkey);
                }

                if (is_Array($result) || is_object($result)) {

                    $order_details->$resultkey = (json_encode($result));
                } else {

                    $order_details->$resultkey = $result;
                }
            }
            R::store($order_details);
        } catch (Exception $e) {
            log::alert($e->getMessage());
        }
    }

    public function getOrderItemsApi($config, $order_id, $seller_id)
    {
        // $host = $this->host;
        // $dbname = $this->dbname;
        // $port = $this->port;
        // $username = $this->username;
        // $password = $this->password;
        // R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        
        Log::debug("Works");
        $apiInstance = new OrdersApi($config);
        try {
            $results = $apiInstance->getOrderItems($order_id)->getPayload();
            $results = json_decode(json_encode($results));

            foreach ($results as $result_key => $result_details) {
                if (is_array($result_details)) {
                    foreach ($result_details as $data_key => $data_details) {

                        $order_items = R::dispense('orderitems');
                        $order_items->seller_identifier = $seller_id;
                        $order_items->order_identifier = $order_id;

                        if (is_array($data_details) || is_object($data_details)) {
                            foreach ((array)$data_details as $item_key => $item_details) {
                                $search = 'Id';
                                $replaceVal = 'Identifier';
                                if($item_key == "ASIN"){
                                    $item_key = 'asin';
                                }
                                $item_key = lcfirst($item_key);

                                if (substr($item_key, -2) == 'Id') {

                                    $item_key = str_replace($search, $replaceVal, $item_key);
                                }
                                
                                if (is_array($item_details) || is_object($item_details)) {

                                    $order_items->$item_key = json_encode($item_details);
                                  
                                } else {

                                    $order_items->$item_key = $item_details;
                                }
                            }
                        }
                        R::store($order_items);
                    }
                }
            }
        } catch (Exception $e) {
            
            log::alert($e->getMessage());
        }
    }
}
