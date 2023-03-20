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
