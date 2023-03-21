<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use GuzzleHttp\Client;
use League\Csv\Reader;
use League\Csv\Writer;
use Carbon\CarbonPeriod;
use App\Models\TestMongo;
use App\Models\MongoDB\zoho;
use GuzzleHttp\Psr7\Request;
use App\Models\FileManagement;
use App\Models\Catalog\catalogae;
use App\Models\Catalog\catalogin;
use App\Models\Catalog\catalogsa;
use App\Models\Catalog\catalogus;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\ProcessManagement;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_us;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_source;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\Catalog\PriceConversion;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use App\Models\ShipNTrack\Aramex\AramexTracking;
use App\Models\ShipNTrack\Aramex\AramexTrackings;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Modal;

Route::get('test/mongo', function () {

    $record = [];

    $field_states = '$field_states';
    $state = '$state';
    $process_flow = '$process_flow';
    $approval = '$approval';
    $review_process = '$review_process';
    $orchestration = '$orchestration';
    $currency_symbol = '$currency_symbol';
    $converted = '$converted';
    $approved = '$approved';
    $editable = '$editable';
    $zia_owner_assignment = '$zia_owner_assignment';
    $review = '$review';
    $converted_detail = '$converted_detail';
    $in_merge = '$in_merge';
    $approval_state = '$approval_state';

    $prod_array = [

        "Address" => "",
        "$field_states" => "",
        "VAT" => "",
        "India_Shipping_Weight_Gr" => "",
        "$state" => "",
        "$process_flow" => "",
        "T_Claim_Follow_Up_Status" => "",
        "Reverse_Pickup_Token_ID" => "",
        "id" => "",
        "Data_Source" => "",
        "RTO_RMA_Date" => "",
        "$approval" => "",

        "Item_Type" => "",
        "US_EDD1" => "",
        "First_Visited_URL" => "",
        "Created_Time" => "",
        "Procurement_URL" => "",
        "Exchange" => "",
        "Refund" => "",
        "Purchased_By" => "",
        "Marketplace_S_Tax" => "",
        "Local_Shipping" => "",
        "Last_Visited_Time" => "",
        "Created_By" => "",

        "Bombino_Shipping_Date" => "",
        "Customer_Type1" => "",
        "Product_Category" => "",
        "Refund_Date" => "",
        "Refund_Reference_Number" => "",
        "Description" => "",
        "Card_Used" => "",
        "Number_Of_Chats" => "",
        "GSTN" => "",
        "$review_process" => [
            "approve" => "",
            "reject" => "",
            "resubmit" => ""
        ],

        "Average_Time_Spent_Minutes" => "",
        "Salutation" => "",
        "Lead_Status" => "",
        "Full_Name" => "",
        "Record_Image" => "",
        "Inventory_Status" => "",
        "Adjustment_Against_Order" => "",
        "Product_Code" => "",
        "Refund_Amount" => "",
        "International_Shipping_INR" => "",
        "Order_Number" => "",
        "Seller_Commission" => "",
        "Width" => "",
        "Nature" => "",
        "Amount_Paid_by_Customer" => "",
        "Designation" => "",
        "T_Claim_Status" => "",
        "Payment_Reference_Number1" => "",
        "Product_Cost_INR" => "",
        "Mobile" => "",
        "$orchestration" => "",
        "US_Shipping_Date" => "",
        "Fulfilment_Channel" => "",
        "Product_Cost" => "",
        "CC_Reference_Number" => "",
        "Length" => "",
        "Procured_From" => "",
        "Lead_Source" => "",
        "Tag" => "",

        "Last_Enriched_Time__s" => "",
        "Bombino_Shipment_ID" => "",
        "Gift_Card_in" => "",
        "US_Refund_Source" => "",
        "Email" => "",
        "$currency_symbol" => "",
        "CC_Charge_Date" => "",
        "Visitor_Score" => "",
        "T_Claim_Date" => "",
        "Purchase_Date" => "",
        "Last_Activity_Time" => "",
        "Payment_Date" => "",
        "H_Code" => "",
        "Unsubscribed_Mode" => "",
        "Step_Down_Inverter" => "",
        "US_Tracking_Number" => "",
        "$converted" => "",
        "Order_Creation_Date" => "",
        "Zip_Code" => "",
        "US_Shipper" => "",
        "Refunded_by_US_Seller" => "",
        "Service_Charges" => "",
        "$approved" => "",
        "MP_Remitted_Amount" => "",
        "India_Tracking_Number" => "",
        "Enrich_Status__s" => "",
        "Days_Visited" => "",
        "India_Shipping_Date" => "",
        "Follow_up_Status" => "",
        "Weight_in_LBS" => "",
        "$editable" => "",
        "City" => "",
        "US_EDD" => "",
        "US_Courier_Name" => "",
        "CC_in" => "",
        "US_Seller_Refund_Date" => "",
        "T_Claim_Amount" => "",
        "State" => "",
        "Purchase_Reference_Number" => "",
        "Procurement_Weight" => "",
        "Product_Link" => "",
        "$zia_owner_assignment" => "",
        "Secondary_Email" => "",
        "Bombino_Shipping_Weight_LBS" => "",
        "Payment_Reference_Number" => "",
        "Paid_By" => "",
        "India_Courier_Name" => "",
        "ASIN" => "",
        "International_Shipment_ID" => "",
        "Duty_Taxes_INR" => "",
        "First_Name" => "",
        "Delivered_to_Customer" => "",
        "$review" => "",
        "Commission_on_Can_Ref" => "",
        "SABS_Invoice_ID" => "",
        "Phone" => "",
        "Bredth" => "",
        "International_Courier_Name" => "",
        "Modified_Time" => "",
        "$converted_detail" => "",

        "Unsubscribed_Time" => "",
        "Quantity" => "",
        "GST" => "",
        "Inventory_Allocation_ID" => "",
        "Seller_Name" => "",
        "Shipping_Weight_in_Grms" => "",
        "First_Visited_Time" => "",
        "TCS_AMOUNT" => "",
        "Last_Name" => "",
        "$in_merge" => "",
        "Step_Down_Inventory_ID" => "",
        "Referrer" => "",
        "Formula_2" => "",
        "SKU" => "",
        "$approval_state" => "",
        "Alternate_Order_No" => "",
        "Inventory_Followup_Status" => ""
    ];

    $var_key = [

        "_id" => '$record["_id"]',
        "ASIN" => '$record["ASIN"]',
        "Alternate_Order_No" => '$record["Alternate_Order_No"]',
        "$converted" => '$record["$converted"]',
        "Address" => '$record["Address"]',
        "Adjustment_Against_Order" => '$record["Adjustment_Against_Order"]',
        "Amount_Paid_by_Customer" => '$record["Amount_Paid_by_Customer"]',
        "Annual_Revenue" => '$record["Annual_Revenue"]',
        "Average_Time_Spent_Minutes" => '$record["Average_Time_Spent_Minutes"]',
        "Bombino_Shipment_ID" => '$record["Bombino_Shipment_ID"]',
        "Bombino_Shipping_Date" => '$record["Bombino_Shipping_Date"]',
        "Bombino_Shipping_Weight_LBS" => '$record["Bombino_Shipping_Weight_LBS"]',
        "Bredth" => '$record["Bredth"]',
        "CC_Charge_Date" => '$record["CC_Charge_Date"]',
        "CC_Reference_Number" => '$record["CC_Reference_Number"]',
        "CC_in" => '$record["CC_in"]',
        "Campaign_Source" => '$record["Campaign_Source"]',
        "Card_Used" => '$record["Card_Used"]',
        "Change_Log_Time__s" => '$record["Change_Log_Time__s"]',
        "City" => '$record["City"]',
        "Commission_on_Can_Ref" => '$record["Commission_on_Can_Ref"]',
        "Company" => '$record["Company"]',
        "Converted_Account" => '$record["Converted_Account"]',
        "Converted_Contact" => '$record["Converted_Contact"]',
        "Converted_Date_Time" => '$record["Converted_Date_Time"]',
        "Converted_Deal" => '$record["Converted_Deal"]',
        "Converted__s" => '$record["Converted__s"]',
        "Country" => '$record["Country"]',
        "Created_By" => '$record["Created_By"]',
        "Created_Time" => '$record["Created_Time"]',
        "Currency" => '$record["Currency"]',
        "Customer_Type1" => '$record["Customer_Type1"]',
        "Days_Visited" => '$record["Days_Visited"]',
        "Delivered_to_Customer" => '$record["Delivered_to_Customer"]',
        "Description" => '$record["Description"]',
        "Designation" => '$record["Designation"]',
        "Duty_Taxes_INR" => '$record["Duty_Taxes_INR"]',
        "Email" => '$record["Email"]',
        "Email_Opt_Out" => '$record["Email_Opt_Out"]',
        "Enrich_Status__s" => '$record["Enrich_Status__s"]',
        "Exchange" => '$record["Exchange"]',
        "Exchange_Rate" => '$record["Exchange_Rate"]',
        "Fax" => '$record["Fax"]',
        "First_Name" => '$record["First_Name"]',
        "First_Visited_Time" => '$record["First_Visited_Time"]',
        "First_Visited_URL" => '$record["First_Visited_URL"]',
        "Follow_up_Status" => '$record["Follow_up_Status"]',
        "Formula_2" => '$record["Formula_2"]',
        "Fulfilment_Channel" => '$record["Fulfilment_Channel"]',
        "Full_Name" => '$record["Full_Name"]',
        "GST" => '$record["GST"]',
        "GSTN" => '$record["GSTN"]',
        "Gift_Card_in" => '$record["Gift_Card_in"]',
        "H_Code" => '$record["H_Code"]',
        "Id" => '$record["Id"]',
        "India_Courier_Name" => '$record["India_Courier_Name"]',
        "India_Courier_Name1" => '$record["India_Courier_Name1"]',
        "India_Shipping_Date" => '$record["India_Shipping_Date"]',
        "India_Shipping_Date1" => '$record["India_Shipping_Date1"]',
        "India_Shipping_Weight_Gr" => '$record["India_Shipping_Weight_Gr"]',
        "India_Tracking_Number" => '$record["India_Tracking_Number"]',
        "India_Tracking_Number1" => '$record["India_Tracking_Number1"]',
        "Industry" => '$record["Industry"]',
        "International_Courier_Name" => '$record["International_Courier_Name"]',
        "International_Shipment_ID" => '$record["International_Shipment_ID"]',
        "International_Shipping_INR" => '$record["International_Shipping_INR"]',
        "Inventory_Allocation_ID" => '$record["Inventory_Allocation_ID"]',
        "Inventory_Followup_Status" => '$record["Inventory_Followup_Status"]',
        "Inventory_Status" => '$record["Inventory_Status"]',
        "Is_Record_Duplicate" => '$record["Is_Record_Duplicate"]',
        "Item_Type" => '$record["Item_Type"]',
        "LAST_ACTION" => '$record["LAST_ACTION"]',
        "LAST_ACTION_TIME" => '$record["LAST_ACTION_TIME"]',
        "LAST_SENT_TIME" => '$record["LAST_SENT_TIME"]',
        "Last_Activity_Time" => '$record["Last_Activity_Time"]',
        "Last_Enriched_Time__s" => '$record["Last_Enriched_Time__s"]',
        "Last_Name" => '$record["Last_Name"]',
        "Last_Visited_Time" => '$record["Last_Visited_Time"]',
        "Layout" => '$record["Layout"]',
        "Lead_Conversion_Time" => '$record["Lead_Conversion_Time"]',
        "Lead_Source" => '$record["Lead_Source"]',
        "Lead_Status" => '$record["Lead_Status"]',
        "Length" => '$record["Length"]',
        "Local_Shipping" => '$record["Local_Shipping"]',
        "Locked__s" => '$record["Locked__s"]',
        "MP_Remitted_Amount" => '$record["MP_Remitted_Amount"]',
        "Marketplace_S_Tax" => '$record["Marketplace_S_Tax"]',
        "Mobile" => '$record["Mobile"]',
        "Modified_By" => '$record["Modified_By"]',
        "Modified_Time" => '$record["Modified_Time"]',
        "Nature" => '$record["Nature"]',
        "Negative_Score" => '$record["Negative_Score"]',
        "Negative_Touch_Point_Score" => '$record["Negative_Touch_Point_Score"]',
        "No_of_Employees" => '$record["No_of_Employees"]',
        "Number_Of_Chats" => '$record["Number_Of_Chats"]',
        "Order_Creation_Date" => '$record["Order_Creation_Date"]',
        "Order_Number" => '$record["Order_Number"]',
        "Owner" => '$record["Owner"]',
        "Paid_By" => '$record["Paid_By"]',
        "Payment_Date" => '$record["Payment_Date"]',
        "Payment_Reference_Number" => '$record["Payment_Reference_Number"]',
        "Payment_Reference_Number1" => '$record["Payment_Reference_Number1"]',
        "Phone" => '$record["Phone"]',
        "Positive_Score" => '$record["Positive_Score"]',
        "Positive_Touch_Point_Score" => '$record["Positive_Touch_Point_Score"]',
        "Procured_From" => '$record["Procured_From"]',
        "Procurement_URL" => '$record["Procurement_URL"]',
        "Procurement_Weight" => '$record["Procurement_Weight"]',
        "Product_Category" => '$record["Product_Category"]',
        "Product_Code" => '$record["Product_Code"]',
        "Product_Cost" => '$record["Product_Cost"]',
        "Product_Cost_INR" => '$record["Product_Cost_INR"]',
        "Product_Link" => '$record["Product_Link"]',
        "Purchase_Date" => '$record["Purchase_Date"]',
        "Purchase_Reference_Number" => '$record["Purchase_Reference_Number"]',
        "Purchased_By" => '$record["Purchased_By"]',
        "Quantity" => '$record["Quantity"]',
        "RTO_RMA_Date" => '$record["RTO_RMA_Date"]',
        "Rating" => '$record["Rating"]',
        "Record_Approval_Status" => '$record["Record_Approval_Status"]',
        "Record_Image" => '$record["Record_Image"]',
        "Referrer" => '$record["Referrer"]',
        "Refund" => '$record["Refund"]',
        "Refund_Amount" => '$record["Refund_Amount"]',
        "Refund_Date" => '$record["Refund_Date"]',
        "Refund_Reference_Number" => '$record["Refund_Reference_Number"]',
        "Refunded_by_US_Seller" => '$record["Refunded_by_US_Seller"]',
        "Reverse_Pickup_Token_ID" => '$record["Reverse_Pickup_Token_ID"]',
        "SABS_Invoice_ID" => '$record["SABS_Invoice_ID"]',
        "SKU" => '$record["SKU"]',
        "Salutation" => '$record["Salutation"]',
        "Score" => '$record["Score"]',
        "Secondary_Email" => '$record["Secondary_Email"]',
        "Seller_Commission" => '$record["Seller_Commission"]',
        "Seller_Name" => '$record["Seller_Name"]',
        "Service_Charges" => '$record["Service_Charges"]',
        "Shipping_Weight_in_Grms" => '$record["Shipping_Weight_in_Grms"]',
        "Skype_ID" => '$record["Skype_ID"]',
        "State" => '$record["State"]',
        "Step_Down_Inventory_ID" => '$record["Step_Down_Inventory_ID"]',
        "Step_Down_Inverter" => '$record["Step_Down_Inverter"]',
        "Street" => '$record["Street"]',
        "System_Modified_Time" => '$record["System_Modified_Time"]',
        "System_Related_Activity_Time" => '$record["System_Related_Activity_Time"]',
        "TCS_AMOUNT" => '$record["TCS_AMOUNT"]',
        "T_Claim_Amount" => '$record["T_Claim_Amount"]',
        "T_Claim_Date" => '$record["T_Claim_Date"]',
        "T_Claim_Follow_Up_Status" => '$record["T_Claim_Follow_Up_Status"]',
        "T_Claim_Status" => '$record["T_Claim_Status"]',
        "Tag" => '$record["Tag"]',
        "Territories" => '$record["Territories"]',
        "Touch_Point_Score" => '$record["Touch_Point_Score"]',
        "Twitter" => '$record["Twitter"]',
        "US_Courier_Name" => '$record["US_Courier_Name"]',
        "US_EDD" => '$record["US_EDD"]',
        "US_EDD1" => '$record["US_EDD1"]',
        "US_Refund_Source" => '$record["US_Refund_Source"]',
        "US_Seller_Refund_Date" => '$record["US_Seller_Refund_Date"]',
        "US_Shipper" => '$record["US_Shipper"]',
        "US_Shipping_Date" => '$record["US_Shipping_Date"]',
        "US_Tracking_Number" => '$record["US_Tracking_Number"]',
        "Unsubscribed_Mode" => '$record["Unsubscribed_Mode"]',
        "Unsubscribed_Time" => '$record["Unsubscribed_Time"]',
        "User_Modified_Time" => '$record["User_Modified_Time"]',
        "User_Related_Activity_Time" => '$record["User_Related_Activity_Time"]',
        "VAT" => '$record["VAT"]',
        "Visitor_Score" => '$record["Visitor_Score"]',
        "Website" => '$record["Website"]',
        "Weight_in_LBS" => '$record["Weight_in_LBS"]',
        "Width" => '$record["Width"]',
        "Zip_Code" => '$record["Zip_Code"]',
        "updated_at" => '$record["updated_at"]'
    ];

    $var = [];

    $new = [];

    foreach ($var_key as $key => $prod) {

        if (isset($prod_array[$key])) {

            $var[$key] = $prod;
        } else {
            $new[$key] = $prod ?? "NOT FOUND";
        }
    }

    echo "<pre>";
    print_r($var);
    print_r($new);

    exit;



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
    $start_time = "2022-04-01 00:00:00";
    $end_time = "2023-03-01 00:00:00";
    // $zoho = zoho::limit(50)->orderBy('Created_Time', 'DESC')->get()->toArray();
    // foreach ($Lead_Sources as $Lead_Source) {

    $zoho = zoho::whereBetween('Created_Time', [$start_time, $end_time])->whereIn('Lead_Source', $Lead_Sources)->orderBy('Created_Time', 'DESC')->limit(1000)->get()->toArray();
    // }
    // po($zoho);
    // exit;
    foreach ($zoho as $data) {

        // po($data['_id']['Lead_Source']);
        po($data);
    }
});

Route::get('test/shipntrack/data', function () {


    $records = IntoAE::with(['CourierPartner1', 'CourierPartner2'])
        ->orWhere('forwarder_1_flag', 0)
        ->orWhere('forwarder_2_flag', 0)
        ->get()
        ->toArray();

    po($records);
    exit;
    $records = IntoAE::with(['CourierPartner1', 'CourierPartner2'])
        ->where('awb_number', '1000000000')
        ->get()
        ->toArray();
    foreach ($records as $record) {
        if ($record['forwarder_1_flag'] == 0) {
            po($record['forwarder_1_awb']);
            po($record['courier_partner1']['key1']);
            po($record['courier_partner1']['key2']);
        }
        po($record);
    }
});

Route::get('test/shipntrack/aramex', function () {

    $url = "https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc/json/TrackShipments";
    $payload =
        [
            "ClientInfo" => [
                "UserName" => "mp@moshecom.com",
                "Password" => "A#mazon170",
                "Version" => "v1.0",
                "AccountNumber" => "60531487",
                "AccountPin" => "654654",
                "AccountEntity" => "BOM",
                "AccountCountryCode" => "IN",
                "Source" => 24
            ],
            "GetLastTrackingUpdateOnly" => false,
            "Shipments" => [
                "35124730631"
            ]
        ];

    $response = Http::withoutVerifying()->withHeaders([
        "Content-Type" => "application/json"
    ])->post($url, $payload);

    $aramex_records = [];
    $aramex_data = isset(json_decode($response, true)['TrackingResults'][0]['Value']) ? json_decode($response, true)['TrackingResults'][0]['Value'] : [];
    foreach ($aramex_data as $key1 => $aramex_value) {
        foreach ($aramex_value as $key2 => $value) {

            $aramex_records[$key1]['account_id'] = '1';
            $key2 = ($key2 == 'WaybillNumber')     ? 'awbno'              : $key2;
            $key2 = ($key2 == 'UpdateCode')        ? 'update_code'        : $key2;
            $key2 = ($key2 == 'UpdateDescription') ? 'update_description' : $key2;
            $key2 = ($key2 == 'UpdateDateTime')    ? 'update_date_time'   : $key2;
            $key2 = ($key2 == 'UpdateLocation')    ? 'update_location'    : $key2;
            $key2 = ($key2 == 'Comments')          ? 'comment'            : $key2;
            $key2 = ($key2 == 'ProblemCode')       ? 'problem_code'       : $key2;
            $key2 = ($key2 == 'GrossWeight')       ? 'gross_weight'       : $key2;
            $key2 = ($key2 == 'ChargeableWeight')  ? 'chargeable_weight'  : $key2;
            $key2 = ($key2 == 'WeightUnit')        ? 'weight_unit'        : $key2;

            if ($key2 == 'update_date_time') {
                po($value);
                preg_match('/(\d{10})(\d{3})([\+\-]\d{4})/', $value, $matches);
                $dt = DateTime::createFromFormat("U.u.O", vsprintf('%2$s.%3$s.%4$s', $matches));
                $dt->setTimeZone(new DateTimeZone('Asia/Dubai'));
                $date = $dt->format('Y-m-d H:i:s');

                $aramex_records[$key1][$key2] = $date;
            } else {

                $aramex_records[$key1][$key2] = $value;
            }
        }
    }
    po($aramex_records);
    AramexTrackings::upsert($aramex_records, ['awbno_update_timestamp_description_unique'], [
        'account_id',
        'awbno',
        'update_code',
        'update_description',
        'update_date_time',
        'update_location',
        'comment',
        'gross_weight',
        'chargeable_weight',
        'weight_unit',
        'problem_code'
    ]);
});

Route::get('test/shipntrack/smsa', function () {

    $client = new Client();
    $headers = [
        'Content-Type' => 'text/xml'
    ];
    $body = '<?xml version=\'1.0\' encoding=\'utf-8\'?>
                <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <getTracking xmlns="http://track.smsaexpress.com/secom/">
                    <awbNo>290410158941</awbNo>
                    <passkey>BeL@3845</passkey>
                    </getTracking>
                </soap:Body>
                </soap:Envelope>';
    $request = new Request('POST', 'http://track.smsaexpress.com/SeCom/SMSAwebService.asmx', $headers, $body);
    $response1 = $client->sendAsync($request)->wait();
    $plainXML = mungXML(trim($response1->getBody()));
    $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    // po($arrayResult);
    // exit;
    $smsa_data = $arrayResult['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram']['NewDataSet']['Tracking'];

    $smsa_records = [];
    if (isset($smsa_data[0])) {

        foreach ($smsa_data as $smsa_value) {
            $smsa_records[] = [
                'account_id' => 'smsaUSA',
                'awbno' => $smsa_value['awbNo'] ?? $smsa_data['awbNo'],
                'date' => date('Y-m-d H:i:s', strtotime($smsa_value['Date'] ?? $smsa_data['Date'])),
                'activity' => $smsa_value['Activity'] ?? $smsa_data['Activity'],
                'details' => $smsa_value['Details'] ?? $smsa_data['Details'],
                'location' => $smsa_value['Location'] ?? $smsa_data['Location']
            ];
        }
    } else {
        $smsa_records[] = [
            'account_id' => 'smsaUSA',
            'awbno' =>  $smsa_data['awbNo'],
            'date' => date('Y-m-d H:i:s', strtotime($smsa_data['Date'])),
            'activity' =>  $smsa_data['Activity'],
            'details' =>  $smsa_data['Details'],
            'location' =>  $smsa_data['Location']
        ];
    }

    po($smsa_records);
    exit;
    SmsaTrackings::upsert($smsa_records, ['awbno_date_activity_unique'], [
        'account_id',
        'awbno',
        'date',
        'activity',
        'details',
        'location',
    ]);
});



Route::get('zoho/index', 'VikeshTestController@index');
Route::get('zoho/test', 'VikeshTestController@ReadZohoTextFile');

Route::get('zoho/dump', function () {
    $token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];

    $payload = [
        "query" => [
            "module" => "Leads",
            "page" => 1
        ]
    ];
    $url = "https://www.zohoapis.com/crm/bulk/v2/read";

    $headers = Http::withoutVerifying()->withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $token,
        "Content-Type" => "application/json"
    ])->post($url, $payload);

    $response = $headers->json();
    if (!Storage::exists('ZohoResponse/zoho-response1.txt')) {
        Storage::put('ZohoResponse/zoho-response1.txt', json_encode($response));
    }
    po($response);
});

Route::get('zoho/dump2', function () {
    $token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];
    $url = "https://www.zohoapis.com/crm/bulk/v2/read";

    $zohoResponse =  json_decode(Storage::get('ZohoResponse/zoho-response1.txt', true));
    $requestId = $zohoResponse->data[0]->details->id;

    $requestResponse = Http::withoutVerifying()->withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $token
    ])->get($url . "/" . $requestId);

    po($requestResponse->json());
    Storage::put('ZohoResponse/zoho-response2.txt', json_encode($requestResponse->json()));
    po($requestId);
});

Route::get('zoho/dump3', function () {

    $processManagementID = ProcessManagement::where('module', 'Zoho Dump')
        ->where('command_name', 'mosh:submit-request-to-zoho')
        ->where('command_end_time', '0000-00-00 00:00:00')
        ->get('id')
        ->first();

    po($processManagementID['id']);
    exit;

    $records = zoho::select(['ASIN', 'Alternate_Order_No', 'updated_at', 'Created_Time'])->limit(1000)->orderBy('Created_Time', 'DESC')->get()->toArray();

    if (!empty($records)) {

        po(($records));
    }
    exit;

    $data = CSV_Reader('zohocsv/1929333000107582112.csv');
    $count = 0;
    $result = [];
    $asin = [];
    $order_no = [];

    foreach ($data as  $record) {

        $result[] = $record;
        $asin[] = $record['ASIN'];
        $order_no[] = $record['Alternate_Order_No'];
        $unique[] = [
            'ASIN' => $record['ASIN'],
            'Alternate_Order_No' => $record['Alternate_Order_No']
        ];
        // TestMongo::where('ASIN', $record['ASIN'])->where('Alternate_Order_No', $record['Alternate_Order_No'])->update($record, ['upsert' => true]);
        // po($asin);
        // DB::connection('mongodb')->collection('zoho')->updateMany('ASIN', ['$in' => $record['ASIN']], ['$set', $record], ['upsert' => true]);
        TestMongo::where('Alternate_Order_No_1_ASIN_1', $unique)->update($record, ['upsert' => true]);
        po($result);
        // if ($count == 10) {

        //     // TestMongo::insert($result);
        //     // TestMongo::where('ASIN', $asin)->where('Alternate_Order_No', $order_no)->update($result, ['upsert' => true]);
        //     $count = 0;
        //     $result = [];
        //     // exit;
        // }
        // $count++;
    }
    // TestMongo::insert($result);
    // TestMongo::whereIn('ASIN', $asin)->whereIn('Alternate_Order_No', $order_no)->update($result, ['upsert' => true]);
    // po($asin);
    po($order_no);
    exit;
    $zohoResponse =  json_decode(Storage::get('ZohoResponse/zoho-response2.txt', true));
    po($zohoResponse);
});

Route::get('export', function () {
    $priority = 3;
    $query_limit = 5000;
    $us_destination  = table_model_create(country_code: 'in', model: 'Asin_destination', table_name: 'asin_destination_');
    $asin = $us_destination->select('asin', 'priority')
        ->when($priority != 'All', function ($query) use ($priority) {
            return $query->where('priority', $priority);
        })
        ->where('export', 0)
        ->orderBy('id', 'asc')
        ->limit($query_limit)
        ->get()
        ->toArray();

    $where_asin = [];
    foreach ($asin as $value) {
        $where_asin[] = $value['asin'];
    }

    $pricing_details = PricingIn::join("catalogins", "catalogins.asin", "pricing_ins.asin")
        ->select(["catalogins.length", "catalogins.width", "catalogins.height", "catalogins.weight", "pricing_ins.asin", "pricing_ins.available", "pricing_ins.in_price", "pricing_ins.updated_at"])
        ->whereIn('pricing_ins.asin', $where_asin)
        ->get()
        ->toArray();
    po($pricing_details);
    exit;
    $chunk = 1000;
    $total =  DB::connection('catalog')->select("SELECT cat.asin 
    FROM asin_source_ins as source 
    RIGHT JOIN catalognewins as cat 
    ON cat.asin=source.asin 
    WHERE source.asin IS NULL
   ");

    $loop = ceil(count($total) / $chunk);
    for ($i = 0; $i < $loop; $i++) {

        $data =  DB::connection('catalog')->select("SELECT cat.asin 
        FROM asin_source_ins as source 
        RIGHT JOIN catalognewins as cat 
        ON cat.asin=source.asin 
        WHERE source.asin IS NULL
        LIMIT 1000");
        $asin = [];
        foreach ($data as $record) {
            $asin[] = [
                'asin' => $record->asin,
                'user_id' => '13',
                'status' => 0
            ];
        }
        $table = table_model_create(country_code: 'in', model: 'Asin_source', table_name: 'asin_source_');
        $table->upsert($asin, ['user_asin_unique'], ['asin', 'status']);
        Log::warning('successfully' . $i);
    }
});

Route::get('test', function () {
    $new_offer_lists = ['4', '3', '2', '3'];
    $highest_amount = min($new_offer_lists);
    po($highest_amount);
    po($new_offer_lists);
    $key = min(array_keys($new_offer_lists, min($new_offer_lists)));
    po(($key));
    // exit;



    exit;
    $date = Carbon::now()->addDays(105);
    $date1 = Carbon::now();
    if ($date <= $date1) {
        echo 'working';
    }
    po($date);
});
