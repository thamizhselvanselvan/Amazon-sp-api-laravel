<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\TestMongo;
use App\Models\MongoDB\zoho;
use GuzzleHttp\Psr7\Request;
use App\Models\Catalog\PricingIn;
use App\Models\ProcessManagement;
use App\Models\ShipNTrack\Courier\StatusManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Models\ShipNTrack\CourierTracking\AramexTracking;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

Route::get('test/code', function () {
    // $coureir_data = ['DATA RECEIVED', 'OUT FOR DELIVERY', 'DELAY'];
    // $coureir_status = [];
    // $status = StatusManagement::where('api_display', 0)->get(['courier_status'])->toArray();
    // foreach ($status as $activity) {
    //     // $coureir_status[] = $activity['courier_status'];
    //     if (in_array($activity['courier_status'], $coureir_data)) {
    //         echo $activity['courier_status'];
    //     }
    // }

    $OrderByColunm = [
        'SMSA' => 'date',
        'Aramex' => 'update_date_time',
        'Bombino' => 'action_date'

    ];

    $selectColumns = [
        'SMSA' => [
            'date',
            'activity',
            'location',
        ],
        'Aramex' => [
            'update_date_time',
            'update_description',
            'update_location',
        ],
        'Bombino' => [
            'action_date',
            'action_time',
            'event_detail',
            'location'
        ],

    ];
    $email = Auth::user()->email;
    $result = Trackingksa::with(['CourierAuth'])
        // ->where('awb_number', '1000000000')
        // ->where('partners.login_email', $email)
        ->get()
        ->toArray();

    po($result);
    exit;

    $awb_no = $result[0]['forwarder_1_awb'];
    $courier_name = $result[0]['courier_partner1']['courier_names']['courier_name'];
    $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

    $forwarder1_record = [];
    $forwarder1_data = $table->select($selectColumns[$courier_name])
        ->where('awbno', $awb_no)
        ->orderBy($OrderByColunm[$courier_name], 'DESC')
        ->get()
        ->toArray();
    po($result);
    exit;
    foreach ($forwarder1_data as $data) {

        po($data);
        $forwarder1_record[] = ['courier_name' => $courier_name, ...$data];
    }
});


Route::get('test/shipntrack/aramex/{awbno}', function ($awbNo) {

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
                "$awbNo"
            ]
        ];

    $response = Http::withoutVerifying()->withHeaders([
        "Content-Type" => "application/json"
    ])->post($url, $payload);

    $aramex_records = [];
    $aramex_data = isset(json_decode($response, true)['TrackingResults'][0]['Value']) ? json_decode($response, true)['TrackingResults'][0]['Value'] : [];
    if ($aramex_data != '') {

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
                    // po($value);
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
    } else {
        echo 'Invalid AWB No.';
    }
    po($aramex_records);
    exit;
    AramexTracking::upsert($aramex_records, ['awbno_update_timestamp_description_unique'], [
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

Route::get('test/shipntrack/smsa/{awbno}', function ($awbNo) {

    $client = new Client();
    $headers = [
        'Content-Type' => 'text/xml'
    ];
    $body = '<?xml version=\'1.0\' encoding=\'utf-8\'?>
                <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <getTracking xmlns="http://track.smsaexpress.com/secom/">
                    <awbNo>' . $awbNo . '</awbNo>
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
    $smsa_data = isset($arrayResult['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram']['NewDataSet']['Tracking']) ? $arrayResult['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram']['NewDataSet']['Tracking'] : [];

    if (!empty($smsa_data)) {

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
        SmsaTracking::upsert($smsa_records, ['awbno_date_activity_unique'], [
            'account_id',
            'awbno',
            'date',
            'activity',
            'details',
            'location',
        ]);
    } else {
        echo 'Invalid AWB No.';
    }
});

Route::get('test/shipntrack/bombino/{awbno}', function ($awbNo) {
    $account_id = "58925";
    $user_id = "58925MBM";
    $password = "123";
    $awbNo = $awbNo;
    $url = "http://api.bombinoexp.in/bombinoapi.svc/Tracking?";

    $response = Http::withoutVerifying()->withHeaders([
        "Content-Type" => "application/json"
    ])->get($url . "AccountId=" . $account_id . "&UserId=" . $user_id . "&Password=" . $password . "&AwbNo=" . $awbNo);

    $bombino_records = json_decode($response, true);
    po(($bombino_records));
    exit;
    $records = $bombino_records['Shipments'][0]['TrackPoints'];
    $Bombino_Data = [

        "awb_no" => $bombino_records['Shipments'][0]['AWBNo'] ?? '',
        "consignee" => $bombino_records['Shipments'][0]['Consignee'] ?? '',
        "destination" => $bombino_records['Shipments'][0]['Destination'] ?? '',
        "hawb_no" => $bombino_records['Shipments'][0]['HAwbNo'] ?? '',
        "origin" => $bombino_records['Shipments'][0]['Origin'] ?? '',
        "ship_date" => date('Y-m-d H:i:s', strtotime($bombino_records['Shipments'][0]['ShipDate'])) ?? '',
        "status" => $bombino_records['Shipments'][0]['Status'],
        "weight" => $bombino_records['Shipments'][0]['Weight'] ?? '',
    ];
    $result = [];
    foreach ($records as $record) {

        $bombino_record = [
            'action_date' => date('Y-m-d', strtotime($record['ActionDate'])) ?? '',
            'action_time' => date('H:i:s', strtotime($record['ActionTime'])) ?? '',
            'event_code' => $record['EventCode'] ?? '',
            'event_detail' => $record['EventDetail'] ?? '',
            'exception' => $record['Exception'] ?? '',
            'location' => $record['Location' ?? '']
        ];
        $result[] = [...$Bombino_Data, ...$bombino_record];
    }
    // BombinoTracking::upsert($result, ['awbno_actionDate_eventDetail_unique'], [
    //     'awb_no',
    //     'consignee',
    //     'consignor',
    //     'destination',
    //     'hawb_no',
    //     'origin',
    //     'ship_date',
    //     'weight',
    //     'action_date',
    //     'action_time',
    //     'event_code',
    //     'event_detail',
    //     'exception',
    //     'location'
    // ]);
    po($result);
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

    $data = CSV_Reader('zohocsv/1929333000110149354.csv');
    $result = [];
    $asin = [];
    $order_no = [];
    $count = 0;
    foreach ($data as  $record) {
        if ($count == 0) {

            po($record);
            // break;
        }
        $count++;
    }
    po($record);
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
