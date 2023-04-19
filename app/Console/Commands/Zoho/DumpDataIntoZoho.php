<?php

namespace App\Console\Commands\Zoho;

use App\Models\MongoDB\zoho;
use App\Services\Zoho\ZohoApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DumpDataIntoZoho extends Command
{
    public $cnt = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:dump-data-into-zoho';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take data from Mongo DB to New ZOHO Account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $zoho = new ZohoApi(new_zoho: false);

        // $files = json_decode(Storage::get("zoho1.json"), true);

        // $array_data = [];

        // foreach($files as $arr) {
        //     $array_data[] = $arr["Id"];
        // }

        // $deletes = array_chunk($array_data, 25);

        // foreach($deletes as $delete) {
        //     $zoho->deleteLead($delete);
        // }
        // $this->info("Finish");
        // exit;

        $Lead_Sources = [
            'CKSHOP-Amazon.in',
            'Amazon.in-Gotech',
            'Gotech-Saudi',
            'Gotech UAE',
            'Amazon.in-MBM',
            'Amazon.ae-MBM',
            'Amazon.sa-MBM',
            'Amazon.ae-Mahzuz',
            'Amazon.sa-Mahzuz',
            'Amazon.in-Nitrous',
            'Flipkart-Cliqkart',
            'Flipkart -Cliqkart',
            'Flipkart-Gotech'
        ];

        $query = startTime();

        $start_time = "2022-04-01 00:00:00";
        $end_time = "2023-03-31 00:00:00";
    
        $mongoDB_data = zoho::whereBetween('Created_Time', [$start_time, $end_time])
        ->whereIn('Lead_Source', $Lead_Sources)
        ->where('nz', 0)
        ->limit(30)
        ->orderBy('Created_Time', 'DESC')->get()->toArray();

        if($mongoDB_data <= 0) {

            //slack Notification 
            slack_notification('app360', 'New Zoho Dump', 'New Zoho Dump Mongo DB. All NZ value became 1');

            return true;
        }
        
        $this->info(" After Query Time " . endTime($query));
        $this->info(" TOTAL COUNT " . count($mongoDB_data));

        $mongo_datas = array_chunk($mongoDB_data, 3);

        foreach ($mongo_datas as $record) {

            if(isset($record[0])) {

                $this->mongo_data_format_and_insert($record[0], $zoho);
            }

            if(isset($record[1])) {

                $this->mongo_data_format_and_insert($record[1], $zoho);
            }

            if(isset($record[2])) {

                $this->mongo_data_format_and_insert($record[1], $zoho);
            }

            $this->cnt++;
        }

        $this->info(" After Inserting Time " . endTime($query));
        $this->info(" TOTAL WOrked ON $this->cnt");

    }

    public function mongo_data_format_and_insert($record, $zoho) {

        $prod_array = [
            "Alternate_Order_No" => $record["Alternate_Order_No"],
            "Address" => $record["Address"],
            "Adjustment_Against_Order" => $record["Adjustment_Against_Order"],
            "CC_Charge_Date" => $record["CC_Charge_Date"],
            "CC_Reference_Number" => $record["CC_Reference_Number"],
            "CC_in" => $record["CC_in"],
            "Card_Used" => $record["Card_Used"],
            "City" => $record["City"],
            "Commission_on_Can_Ref" => $record["Commission_on_Can_Ref"],
            "Delivered_to_Customer" => $record["Delivered_to_Customer"],
            "Designation" => $record["Designation"],
            "Email" => $record["Email"],
            "Enrich_Status__s" => $record["Enrich_Status__s"],
            "Exchange" => $record["Exchange"],
            "First_Name" => $record["First_Name"],
            "Follow_up_Status" => $record["Follow_up_Status"],
            "Full_Name" => $record["Full_Name"],
            "GST" => $record["GST"],
            "Gift_Card_in" => $record["Gift_Card_in"],
            "International_Courier_Name" => $record["International_Courier_Name"],
            "International_Shipment_ID" => $record["International_Shipment_ID"],
            "Inventory_Allocation_ID" => $record["Inventory_Allocation_ID"],
            "Inventory_Followup_Status" => $record["Inventory_Followup_Status"],
            "Inventory_Status" => $record["Inventory_Status"],
            "Last_Activity_Time" => $record["Last_Activity_Time"],
            "Last_Enriched_Time__s" => $record["Last_Enriched_Time__s"],
            "Last_Name" => $record["Last_Name"],
            "Lead_Source" => $record["Lead_Source"],
            "Lead_Status" => $record["Lead_Status"],
            "MP_Remitted_Amount" => $record["MP_Remitted_Amount"],
            "Marketplace_S_Tax" => $record["Marketplace_S_Tax"],
            "Mobile" => $record["Mobile"],
            "Modified_Time" => $record["Modified_Time"],
            "Nature" => $record["Nature"],
            "Order_Creation_Date" => $record["Order_Creation_Date"],
            "Order_Number_N" => $record["Order_Number"],
            "Paid_By" => $record["Paid_By"],
            "Payment_Date" => $record["Payment_Date"],
            "Payment_Reference_Number" => $record["Payment_Reference_Number1"],
            "Procured_From" => $record["Procured_From"],
            "Procurement_Weight" => $record["Procurement_Weight"],
            "Product_Category" => $record["Product_Category"],
            "Product_Cost" => $record["Product_Cost"],
            "Product_Link" => $record["Product_Link"],
            "Purchase_Date" => $record["Purchase_Date"],
            "Purchase_Reference_Number" => $record["Purchase_Reference_Number"],
            "Purchased_By" => $record["Purchased_By"],
            "Record_Image" => $record["Record_Image"],
            "Refund" => $record["Refund"],
            "Refund_Amount" => $record["Refund_Amount"],
            "Refund_Date" => $record["Refund_Date"],
            "Refund_Reference_Number" => $record["Refund_Reference_Number"],
            "Refunded_by_US_Seller" => $record["Refunded_by_US_Seller"],
            "Reverse_Pickup_Token_ID" => $record["Reverse_Pickup_Token_ID"],
            "SABS_Invoice_ID" => $record["SABS_Invoice_ID"],
            "SKU" => $record["SKU"],
            "Salutation" => $record["Salutation"],
            "Seller_Name" => $record["Seller_Name"],
            "State" => $record["State"],
            "Step_Down_Inventory_ID" => $record["Step_Down_Inventory_ID"],
            "Step_Down_Inverter" => $record["Step_Down_Inverter"],
            "TCS_AMOUNT" => $record["TCS_AMOUNT"],
            "T_Claim_Amount" => $record["T_Claim_Amount"],
            "T_Claim_Date" => $record["T_Claim_Date"],
            "T_Claim_Follow_Up_Status" => $record["T_Claim_Follow_Up_Status"],
            "T_Claim_Status" => $record["T_Claim_Status"],
            "Tag" => $record["Tag"],
            "US_Courier_Name" => $record["US_Courier_Name"],
            "US_Refund_Source" => $record["US_Refund_Source"],
            "US_Seller_Refund_Date" => $record["US_Seller_Refund_Date"],
            "US_Shipping_Date" => $record["US_Shipping_Date"],
            "US_Tracking_Number" => $record["US_Tracking_Number"],
            "Unsubscribed_Mode" => $record["Unsubscribed_Mode"],
            "Unsubscribed_Time" => $record["Unsubscribed_Time"],
            "Zip_Code" => $record["Zip_Code"],
           
            "Fulfillment_Center_ID" => $record["ASIN"],
            "EDD" => date("Y-m-d", strtotime($record["US_EDD"])),
            "Product_Qty" => $record["Quantity"],
            "Product_ASIN" => $record["Procurement_URL"],
            "Inv_ID" => $record['Weight_in_LBS'],

            "Fullfilment" => $record["Fulfilment_Channel"],
            "Customer_Type" => $record["Customer_Type1"],
            "Product_SKU" => $record["Product_Code"],
            "Product_Name" => $record["US_Shipper"],
            "International_Shipping_Weight_LBS" =>  (float)$record['Bombino_Shipping_Weight_LBS'],
           // "International_Shipping_Date" => (isset($record['Bombino_Shipping_Date'])) ? date("Y-m-d", strtotime($record["Bombino_Shipping_Date"])) : '',
            "Amount_Paid_by_Customer" => $record['Amount_Paid_by_Customer'],
            "HSN" => $record['H_Code'],

            "Bombino_Local_Tracking" => $record['Bombino_Shipment_ID'],
            "Local_Tracking_Number" => $record['India_Tracking_Number'],
            "Local_Courier_Name" => $record['India_Courier_Name'],
            "Delivered_at_US" => strlen($record['US_EDD1']) > 0 ? date("Y-m-d", strtotime($record['US_EDD1'])) : '',
            "Description" => $record['Description']
        ];

        if(isset($record['India_Shipping_Date'])) {
            $prod_array["Local_Shipping_Date"] = date("Y-m-d", strtotime($record['India_Shipping_Date']));
        }

        if(isset($record['Bombino_Shipping_Date'])) {
            $prod_array["International_Shipping_Date"] = date("Y-m-d", strtotime($record['Bombino_Shipping_Date']));
        }

        $zoho_search_order_exists = $zoho->search($record['Alternate_Order_No'], $record['Payment_Reference_Number1'], 1);

        if(!$zoho_search_order_exists) {

            $zoho_api_save = $zoho->storeLead($prod_array);

            $zoho_response = ($zoho_api_save) ? $zoho_api_save : null;

            if (isset($zoho_response) && gettype($zoho_response) == "array" && array_key_exists('data', $zoho_response) && array_key_exists(0, $zoho_response['data']) && array_key_exists('code', $zoho_response['data'][0])) {

                $this->mongoUpdate($record["_id"]);

                $string = "Inserted ". $prod_array['Alternate_Order_No'] . " $this->cnt";

                $this->info($string);

            } else {
                
                $this->error($record["Order_Number"] . " Order did not save " . " $this->cnt");
                $this->error($zoho_response);

            }

        } else  {

            $zoho_api_update = $zoho->updateLead($zoho_search_order_exists['data'][0]['id'], $prod_array);

            $this->mongoUpdate($record["_id"]);

            $string = "Updated  ". $prod_array['Alternate_Order_No'] . " $this->cnt";
            $this->info($string);
        }

    }

    public function mongoUpdate($id) {

        zoho::where('_id', $id)->update(["nz" => 1]);
    }
}
