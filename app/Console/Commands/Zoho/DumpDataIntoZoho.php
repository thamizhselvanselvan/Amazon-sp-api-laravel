<?php

namespace App\Console\Commands\Zoho;

use App\Models\MongoDB\zoho;
use App\Services\Zoho\ZohoApi;
use Illuminate\Console\Command;
use App\Services\Zoho\ZohoOrder;
use Illuminate\Support\Facades\Log;

class DumpDataIntoZoho extends Command
{
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
        $zoho = new ZohoApi;
        $zohoOrder = new ZohoOrder;

        $Lead_Sources = [
            'CKSHOP-Amazon.in',
            // 'Amazon.in-Gotech',
            // 'Gotech-Saudi',
            // 'Gotech UAE',
            // 'Amazon.in-MBM',
            // 'Amazon.ae-MBM',
            // 'Amazon.sa-MBM',
            // 'Amazon.ae-Mahzuz',
            // 'Amazon.sa-Mahzuz',
            // 'Amazon.in-Nitrous',
            // 'Flipkart-Cliqkart',
            // 'Flipkart -Cliqkart',
            // 'Flipkart-Gotech'
        ];
        $start_time = "2022-04-01 00:00:00";
        $end_time = "2023-03-01 00:00:00";
    
        $mongoDB_data = zoho::whereBetween('Created_Time', [$start_time, $end_time])->whereIn('Lead_Source', $Lead_Sources)->orderBy('Created_Time', 'DESC')->limit(100)->get()->toArray();
            
   
        foreach ($mongoDB_data as $record) {

            $prod_array = [
    
                "ASIN" => $record["ASIN"],
                "Alternate_Order_No" => $record["Alternate_Order_No"],
                '$converted' => $record['$converted'],
                "Address" => $record["Address"],
                "Adjustment_Against_Order" => $record["Adjustment_Against_Order"],
                "Amount_Paid_by_Customer" => $record["Amount_Paid_by_Customer"],
                "Average_Time_Spent_Minutes" => $record["Average_Time_Spent_Minutes"],
                "Bombino_Shipment_ID" => $record["Bombino_Shipment_ID"],
                "Bombino_Shipping_Date" => $record["Bombino_Shipping_Date"],
                "Bombino_Shipping_Weight_LBS" => $record["Bombino_Shipping_Weight_LBS"],
                "Bredth" => $record["Bredth"],
                "CC_Charge_Date" => $record["CC_Charge_Date"],
                "CC_Reference_Number" => $record["CC_Reference_Number"],
                "CC_in" => $record["CC_in"],
                "Card_Used" => $record["Card_Used"],
                "City" => $record["City"],
                "Commission_on_Can_Ref" => $record["Commission_on_Can_Ref"],
                "Created_By" => $record["Created_By"],
                "Created_Time" => $record["Created_Time"],
                "Customer_Type1" => $record["Customer_Type1"],
                "Days_Visited" => $record["Days_Visited"],
                "Delivered_to_Customer" => $record["Delivered_to_Customer"],
                "Description" => $record["Description"],
                "Designation" => $record["Designation"],
                "Duty_Taxes_INR" => $record["Duty_Taxes_INR"],
                "Email" => $record["Email"],
                "Enrich_Status__s" => $record["Enrich_Status__s"],
                "Exchange" => $record["Exchange"],
                "First_Name" => $record["First_Name"],
                "First_Visited_Time" => $record["First_Visited_Time"],
                "First_Visited_URL" => $record["First_Visited_URL"],
                "Follow_up_Status" => $record["Follow_up_Status"],
                "Formula_2" => $record["Formula_2"],
                "Fulfilment_Channel" => $record["Fulfilment_Channel"],
                "Full_Name" => $record["Full_Name"],
                "GST" => $record["GST"],
                "GSTN" => $record["GSTN"],
                "Gift_Card_in" => $record["Gift_Card_in"],
                "H_Code" => $record["H_Code"],
                "India_Courier_Name" => $record["India_Courier_Name"],
                "India_Shipping_Date" => $record["India_Shipping_Date"],
                "India_Shipping_Weight_Gr" => $record["India_Shipping_Weight_Gr"],
                "India_Tracking_Number" => $record["India_Tracking_Number"],
                "International_Courier_Name" => $record["International_Courier_Name"],
                "International_Shipment_ID" => $record["International_Shipment_ID"],
                "International_Shipping_INR" => $record["International_Shipping_INR"],
                "Inventory_Allocation_ID" => $record["Inventory_Allocation_ID"],
                "Inventory_Followup_Status" => $record["Inventory_Followup_Status"],
                "Inventory_Status" => $record["Inventory_Status"],
                "Item_Type" => $record["Item_Type"],
                "Last_Activity_Time" => $record["Last_Activity_Time"],
                "Last_Enriched_Time__s" => $record["Last_Enriched_Time__s"],
                "Last_Name" => $record["Last_Name"],
                "Last_Visited_Time" => $record["Last_Visited_Time"],
                "Lead_Source" => $record["Lead_Source"],
                "Lead_Status" => $record["Lead_Status"],
                "Length" => $record["Length"],
                "Local_Shipping" => $record["Local_Shipping"],
                "MP_Remitted_Amount" => $record["MP_Remitted_Amount"],
                "Marketplace_S_Tax" => $record["Marketplace_S_Tax"],
                "Mobile" => $record["Mobile"],
                "Modified_Time" => $record["Modified_Time"],
                "Nature" => $record["Nature"],
                "Number_Of_Chats" => $record["Number_Of_Chats"],
                "Order_Creation_Date" => $record["Order_Creation_Date"],
                "Order_Number" => $record["Order_Number"],
                "Paid_By" => $record["Paid_By"],
                "Payment_Date" => $record["Payment_Date"],
                "Payment_Reference_Number" => $record["Payment_Reference_Number"],
                "Payment_Reference_Number1" => $record["Payment_Reference_Number1"],
                "Phone" => $record["Phone"],
                "Procured_From" => $record["Procured_From"],
                "Procurement_URL" => $record["Procurement_URL"],
                "Procurement_Weight" => $record["Procurement_Weight"],
                "Product_Category" => $record["Product_Category"],
                "Product_Code" => $record["Product_Code"],
                "Product_Cost" => $record["Product_Cost"],
                "Product_Cost_INR" => $record["Product_Cost_INR"],
                "Product_Link" => $record["Product_Link"],
                "Purchase_Date" => $record["Purchase_Date"],
                "Purchase_Reference_Number" => $record["Purchase_Reference_Number"],
                "Purchased_By" => $record["Purchased_By"],
                "Quantity" => $record["Quantity"],
                "RTO_RMA_Date" => $record["RTO_RMA_Date"],
                "Record_Image" => $record["Record_Image"],
                "Referrer" => $record["Referrer"],
                "Refund" => $record["Refund"],
                "Refund_Amount" => $record["Refund_Amount"],
                "Refund_Date" => $record["Refund_Date"],
                "Refund_Reference_Number" => $record["Refund_Reference_Number"],
                "Refunded_by_US_Seller" => $record["Refunded_by_US_Seller"],
                "Reverse_Pickup_Token_ID" => $record["Reverse_Pickup_Token_ID"],
                "SABS_Invoice_ID" => $record["SABS_Invoice_ID"],
                "SKU" => $record["SKU"],
                "Salutation" => $record["Salutation"],
                "Secondary_Email" => $record["Secondary_Email"],
                "Seller_Commission" => $record["Seller_Commission"],
                "Seller_Name" => $record["Seller_Name"],
                "Service_Charges" => $record["Service_Charges"],
                "Shipping_Weight_in_Grms" => $record["Shipping_Weight_in_Grms"],
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
                "US_EDD" => $record["US_EDD"],
                "US_EDD1" => $record["US_EDD1"],
                "US_Refund_Source" => $record["US_Refund_Source"],
                "US_Seller_Refund_Date" => $record["US_Seller_Refund_Date"],
                "US_Shipper" => $record["US_Shipper"],
                "US_Shipping_Date" => $record["US_Shipping_Date"],
                "US_Tracking_Number" => $record["US_Tracking_Number"],
                "Unsubscribed_Mode" => $record["Unsubscribed_Mode"],
                "Unsubscribed_Time" => $record["Unsubscribed_Time"],
                "VAT" => $record["VAT"],
                "Visitor_Score" => $record["Visitor_Score"],
                "Weight_in_LBS" => $record["Weight_in_LBS"],
                "Width" => $record["Width"],
                "Zip_Code" => $record["Zip_Code"],
                // DONT HAVE BELOW DETAILS
                "Annual_Revenue" => $record["Annual_Revenue"],
                "Campaign_Source" => $record["Campaign_Source"],
                "Change_Log_Time__s" => $record["Change_Log_Time__s"],
                "Company" => $record["Company"],
                "Converted_Account" => $record["Converted_Account"],
                "Converted_Contact" => $record["Converted_Contact"],
                "Converted_Date_Time" => $record["Converted_Date_Time"],
                "Converted_Deal" => $record["Converted_Deal"],
                "Converted__s" => $record["Converted__s"],
                "Country" => $record["Country"],
                "Currency" => $record["Currency"],
                "Email_Opt_Out" => $record["Email_Opt_Out"],
                "Exchange_Rate" => $record["Exchange_Rate"],
                "Fax" => $record["Fax"],
                "Id" => $record["Id"],
                "India_Courier_Name1" => $record["India_Courier_Name1"],
                "India_Shipping_Date1" => $record["India_Shipping_Date1"],
                "India_Tracking_Number1" => $record["India_Tracking_Number1"],
                "Industry" => $record["Industry"],
                "Is_Record_Duplicate" => $record["Is_Record_Duplicate"],
                "LAST_ACTION" => $record["LAST_ACTION"],
                "LAST_ACTION_TIME" => $record["LAST_ACTION_TIME"],
                "LAST_SENT_TIME" => $record["LAST_SENT_TIME"],
                "Layout" => $record["Layout"],
                "Lead_Conversion_Time" => $record["Lead_Conversion_Time"],
                "Locked__s" => $record["Locked__s"],
                "Modified_By" => $record["Modified_By"],
                "Negative_Score" => $record["Negative_Score"],
                "Negative_Touch_Point_Score" => $record["Negative_Touch_Point_Score"],
                "No_of_Employees" => $record["No_of_Employees"],
                "Owner" => $record["Owner"],
                "Positive_Score" => $record["Positive_Score"],
                "Positive_Touch_Point_Score" => $record["Positive_Touch_Point_Score"],
                "Rating" => $record["Rating"],
                "Record_Approval_Status" => $record["Record_Approval_Status"],
                "Score" => $record["Score"],
                "Skype_ID" => $record["Skype_ID"],
                "Street" => $record["Street"],
                "System_Modified_Time" => $record["System_Modified_Time"],
                "System_Related_Activity_Time" => $record["System_Related_Activity_Time"],
                "Territories" => $record["Territories"],
                "Touch_Point_Score" => $record["Touch_Point_Score"],
                "Twitter" => $record["Twitter"],
                "User_Modified_Time" => $record["User_Modified_Time"],
                "User_Related_Activity_Time" => $record["User_Related_Activity_Time"],
                "Website" => $record["Website"]
            
            ];

            $zoho_api_save = $zoho->storeLead($prod_array);

            $this->info($zoho_api_save);

            $zoho_response = ($zoho_api_save) ? $zoho_api_save : null;

            if (isset($zoho_response) && gettype($zoho_response) == "array" && array_key_exists('data', $zoho_response) && array_key_exists(0, $zoho_response['data']) && array_key_exists('code', $zoho_response['data'][0])) {

                $zoho_save_id = $zoho_response['data'][0]['details']['id'];

                $string = "Inserted ".$prod_array['Alternate_Order_No'];

                $this->info($string);

            } else {
             //   Log::error("Zoho Response : " . json_encode($zoho_response));

               // $string = "error ".$prod_array['Alternate_Order_No'];

                $this->error($zoho_response);

            }
    
      
        }
        

    }
}
