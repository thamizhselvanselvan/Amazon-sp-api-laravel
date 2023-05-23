<?php

use Carbon\Carbon;
use App\Models\User;
use Faker\Core\Number;
use League\Csv\Reader;
use League\Csv\Writer;
use Carbon\CarbonInterval;
use App\Models\Aws_credential;
use App\Models\FileManagement;
use PhpParser\Node\Expr\Eval_;
use App\Models\Catalog\Catalog;
use App\Models\Admin\Ratemaster;
use App\Models\CommandScheduler;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemSetting\SystemSetting;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Models\ShipNTrack\CourierTracking\SmsaTrackings;
use App\Models\ShipNTrack\Bombino\BombinoTrackingDetails;
use App\Models\ShipNTrack\CourierTracking\BombinoTracking;
use App\Models\ShipNTrack\CourierTracking\BombinoTrackings;

if (!function_exists('ddp')) {
    function ddp($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }
}

if (!function_exists('addPercentage')) {

    function addPercentage($originalAmount, $percentageChange)
    {
        return (float)$originalAmount + ($percentageChange / 100) * (float)$originalAmount;
    }
}

if (!function_exists('removePercentage')) {
    function removePercentage($originalAmount, $percentageChange)
    {
        return $originalAmount - ($percentageChange / 100) * $originalAmount;
    }
}

if (!function_exists('addPercentage_product_push')) {

    function addPercentage_product_push($originalAmount, $percentageChange)
    {
        return (float)$originalAmount + ($percentageChange / 100) * (float)$originalAmount;
    }
}

if (!function_exists('removePercentage_product_push')) {
    function removePercentage_product_push($originalAmount, $percentageChange)
    {
        return (float)$originalAmount - ($percentageChange / 100) * (float)$originalAmount;
    }
}


if (!function_exists('getPercentageChange')) {

    function getPercentageChange($oldAmount, $newAmount)
    {
        $decreaseValue = $oldAmount - $newAmount;

        return ($decreaseValue / $oldAmount) * 100;
    }
}

if (!function_exists('productDetailsDelete')) {

    function productDetailsDelete($table, $product_id)
    {

        $query = DB::table($table)->where('product_id', $product_id);
        $get_data = $query->get();

        if (!$get_data->isEmpty()) {
            $query->delete();
        }
    }
}

if (!function_exists('productDetailsDeleteWithAsin')) {

    function productDetailsDeleteWithAsin($table, $asin, $country_code)
    {

        $query = DB::table($table)->where('asin', $asin)->where('country_code', $country_code);
        $get_data = $query->get();

        if (!$get_data->isEmpty()) {
            $query->delete();
        }
    }
}

if (!function_exists('productsInsert')) {

    function productsInsert($table, $data)
    {

        DB::table($table)->insert($data);
    }
}

if (!function_exists('productOldDataDeleteAndInsertNewData')) {

    function productOldDataDeleteAndInsertNewData($table, $product_id, $data)
    {

        productDetailsDelete($table, $product_id);
        productsInsert($table, $data);
    }
}

if (!function_exists('productOldDataDeleteAndInsertNewDataASIN')) {

    function productOldDataDeleteAndInsertNewDataASIN($table, $asin, $country_code, $data)
    {

        productDetailsDeleteWithAsin($table, $asin, $country_code);
        productsInsert($table, $data);
    }
}

if (!function_exists('aws_credentials')) {
    function aws_credentials()
    {

        if (Auth::user()->roles->first()->name == "Seller") {

            return Aws_credential::with(['mws_region'])->where('seller_id', Auth::user()->id)->first();
        }
    }
}

if (!function_exists('slack_notification')) {
    function slack_notification($channel, $title, $message)
    {
        switch ($channel) {
            case 'monitor':
                $webhook = 'slack_monitor';
                break;

            case 'buybox':
                $webhook = 'slack_bb';
                break;

            case 'app360':
                $webhook = 'slack_360';
                break;

            case 'aimeos':
                $webhook = 'slack_aimeos';
                break;

            default:
                $webhook = 'slack';
                break;
        }

        $slackMessage = $title;
        $slackMessage .= PHP_EOL;
        $slackMessage .= $message;

        Log::channel($webhook)->error($slackMessage);
    }
}

if (!function_exists('healthCheck')) {
    function healthCheck()
    {
        if (app()->environment('production')) {
            $generalHealthState = app('pragmarx.health')->checkResources();
            $msg = '';
            foreach ($generalHealthState as $obj) {

                if (!$obj->isHealthy()) {
                    $msg .= "{$obj->name}: {$obj->errorMessage}\n";
                }
            }
            if (!empty($msg)) {
                slack_notification("", "Health Check", $msg);
            }
        }
    }
}

if (!function_exists('is_developer')) {
    function is_developer()
    {
        return (in_array(Auth::user()->id, [1, 2])) ? true : false;
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {

        if (Auth::user()->roles->first()->name == "Admin") {

            return true;
        }

        return false;
    }
}

if (!function_exists('startTime')) {
    function startTime()
    {
        return microtime(true);
    }
}

if (!function_exists('endTime')) {
    function endTime($start)
    {
        $time_elapsed_secs = microtime(true) - $start;
        // print("Time elapsed: $time_elapsed_secs");

        return $time_elapsed_secs;
    }
}

if (!function_exists('showBreadcrumb')) {
    function showBreadcrumb()
    {
        return ucwords(str_replace([' ', '-', '_'], ['/', ' ', ' '], ucwords(str_replace(['/'], ' ', Route::current()->uri))));
    }
}

if (!function_exists('productBatch')) {
    function productBatch($products, $asin_limit, $request_no = 5)
    {
        $key = 0;
        $counter = 1;
        $request_counter = 0;
        $product_in_batch = [];

        foreach ($products as $product) {

            if ($counter <= $asin_limit) {

                $product_in_batch[$request_counter][$key][] = $product;
            }

            if ($counter == $asin_limit) {
                $counter = 0;
                $key++;
            }

            if ($request_no < count($product_in_batch[$request_counter])) {
                $request_counter++;
            }


            $counter++;
        }

        return $product_in_batch;
    }
}

if (!function_exists('productLowestPricedOffer')) {
    function productLowestPricedOffer($totalProducts = 200, $credentials = 1)
    {
        $delay = 18;
        $delay_seconds = $delay / $credentials;

        return  $delay_seconds * 1000;
    }
}

if (!function_exists('dateTimeFilter')) {
    function dateTimeFilter($data, $subDays = 6)
    {

        $period = new DatePeriod(Carbon::now()->subDays($subDays), CarbonInterval::day(), Carbon::today()->endOfDay());

        return array_reverse(array_map(function ($datePeriod) use ($data) {
            $date = $datePeriod->format('d-m-Y');
            return (isset($data[$date])) ? $data[$date] : 0;
        }, iterator_to_array($period)));
    }
}

if (!function_exists('dateTimeFiltermonthly')) {
    function dateTimeFiltermonthly($data, $subDays = 30)
    {

        $period = new DatePeriod(Carbon::now()->subDays($subDays), CarbonInterval::day(), Carbon::today()->endOfDay());

        return array_reverse(array_map(function ($datePeriod) use ($data) {
            $date = $datePeriod->format('d-m-Y');
            return (isset($data[$date])) ? $data[$date] : 0;
        }, iterator_to_array($period)));
    }
}

function calcualteAwsSignatureAndReturnHeaders(
    $host,
    $uri,
    $requestUrl,
    $accessKey,
    $secretKey,
    $region,
    $service,
    $httpRequestMethod,
    $data,
    $debug = TRUE
) {

    $terminationString  = 'aws4_request';
    $algorithm          = 'AWS4-HMAC-SHA256';
    $phpAlgorithm       = 'sha256';
    $canonicalURI       = $uri;
    $canonicalQueryString = 'productRegion=US&locale=es_US';
    $signedHeaders      = 'content-type;host;x-amz-date'; //;x-amz-user-email;x-amz-access-token
    $accessToken        = 'Atza|IwEBIGVsmmnXo9b3xSuoeypSSlvQtk37QrrHmabOhogciCW_fEng9G4qxZrvROLTuEOfL8IOWMuYKFp1JlVFH9uQE_0DsyPbVFZSlg4zqsFfEIncmABIL8iSlaHJbzobNiNgifWfzOOqs_nvQSswC6I76qZdDxtsj77KqxX_NJ2kRNe76TnpoECWO7EeSNndk-tsPQZ1Rc-BLHXr09CgXczvZvPXTHHA51wlBbEP3UhPvrM0PY_x0NBSlBOmN7Aipe-z27NNAV8CT7I8I-g66SSUhEIwBdqiWHlqrDrYch_dhPbYnGlrhvUmBSvfF9i3UdTHMWsuHVkTc8IY8o33zU6peEor';

    $currentDateTime = new DateTime('UTC');
    $reqDate = $currentDateTime->format('Ymd');
    $reqDateTime = $currentDateTime->format('Ymd\THis\Z');

    // Create signing key
    $kSecret = 'AWS4' . $secretKey;
    $kDate = hash_hmac($phpAlgorithm, $reqDate, $kSecret, true);
    $kRegion = hash_hmac($phpAlgorithm, $region, $kDate, true);
    $kService = hash_hmac($phpAlgorithm, $service, $kRegion, true);
    $kSigning = hash_hmac($phpAlgorithm, $terminationString, $kService, true);

    // Create canonical headers
    $canonicalHeaders = array();
    $canonicalHeaders[] = 'content-type: application/x-www-form-urlencoded';
    $canonicalHeaders[] = 'host: ' . $host;
    $canonicalHeaders[] = 'x-amz-date: ' . $reqDateTime;
    //  $canonicalHeaders[] = 'x-amz-user-email: nitrouspurchases@gmail.com';
    //  $canonicalHeaders[] = 'x-amz-access-token: ' . $accessToken;
    $canonicalHeadersStr = implode("\n", $canonicalHeaders);

    // Create request payload
    $requestHasedPayload = hash($phpAlgorithm, $data);

    // Create canonical request
    $canonicalRequest = array();
    $canonicalRequest[] = $httpRequestMethod;
    $canonicalRequest[] = $canonicalURI;
    $canonicalRequest[] = $canonicalQueryString;
    $canonicalRequest[] = $canonicalHeadersStr . "\n";
    $canonicalRequest[] = $signedHeaders;
    $canonicalRequest[] = $requestHasedPayload;
    $requestCanonicalRequest = implode("\n", $canonicalRequest);
    $requestHasedCanonicalRequest = hash($phpAlgorithm, utf8_encode($requestCanonicalRequest));
    if ($debug) {
        echo "<h5>Canonical to string</h5>";
        echo "<pre>";
        echo $requestCanonicalRequest;
        echo "</pre>";
    }

    // Create scope
    $credentialScope = array();
    $credentialScope[] = $reqDate;
    $credentialScope[] = $region;
    $credentialScope[] = $service;
    $credentialScope[] = $terminationString;
    $credentialScopeStr = implode('/', $credentialScope);

    // Create string to signing
    $stringToSign = array();
    $stringToSign[] = $algorithm;
    $stringToSign[] = $reqDateTime;
    $stringToSign[] = $credentialScopeStr;
    $stringToSign[] = $requestHasedCanonicalRequest;
    $stringToSignStr = implode("\n", $stringToSign);
    if ($debug) {
        echo "<h5>String to Sign</h5>";
        echo "<pre>";
        echo $stringToSignStr;
        echo "</pre>";
    }

    // Create signature
    $signature = hash_hmac($phpAlgorithm, $stringToSignStr, $kSigning);

    // Create authorization header
    $authorizationHeader = array();
    $authorizationHeader[] = 'Credential=' . $accessKey . '/' . $credentialScopeStr;
    $authorizationHeader[] = 'SignedHeaders=' . $signedHeaders;
    $authorizationHeader[] = 'Signature=' . ($signature);
    $authorizationHeaderStr = $algorithm . ' ' . implode(', ', $authorizationHeader);

    // Request headers
    $headers = array();
    $headers[] = 'authorization: ' . $authorizationHeaderStr;
    //    $headers[] = 'content-length:'.strlen($data);
    $headers[] = 'content-type: application/x-www-form-urlencoded';
    $headers[] = 'host: ' . $host;
    $headers[] = 'x-amz-date: ' . $reqDateTime;
    $headers[] = 'x-amz-user-email: nitrouspurchases@gmail.com';
    $headers[] = 'x-amz-access-token: ' . $accessToken;

    return $headers;
}

function apiCall($header)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://na.business-api.amazon.com/products/2020-08-26/products/B081G4G8N8?productRegion=US&locale=es_US',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $header,
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    dd($header, $response);
}

/**
 * This function is in use
 * for send request with authorization header
 */
function callToAPI($requestUrl, $httpRequestMethod, $headers, $data, $debug = TRUE)
{

    $response = Http::withHeaders($headers)->get($requestUrl);

    if ($response->successful()) {
        echo "OK";
    } else {
        dd($headers, $response->body(), $response->headers(), $response->object());
    }
} // End callToAPI




//$host = "na.business-api.amazon.com";
//$requestUrl = "https://na.business-api.amazon.com";


function signRequest()
{
    $method = 'GET';
    $uri = '/products/2020-08-26/products';
    //$json = file_get_contents('php://input');
    //$obj = json_decode($json);
    $param = '';

    // if(isset($obj->method))
    // {
    //     $m = explode("|", $obj->method);
    //     $method = $m[0];
    //     $uri .= $m[1];
    // }

    $secretKey = 'zjYimrzHWwT3eA3eKkuCGxMb+OA2fibMivnnht3t';
    $access_key = 'AKIARVGPJZCJHLW5MH63';
    $token = 'Atza|IwEBIBmeBZ3mcWW8RekHjmTsLuHnenrYAwcbZiC-DoHLWDV-i1k7JC8qrJY50HANhHSoBB0_AtbBTDdQZx47ben7Hf031KAJe2b5FCmLtqlVsb3cakXlV3knVdq20EsmMsK3CWOh55EJbN6sU2BckIJKjih2eZmf04jpCooPl5JH2sWVsmNzhBcXza8q_cZwNjUeS0lPY5aMMxlLqAdBYLQG5ycXIdzKUEimEq57DeA2QV9neMsO-i_Xzk3HZpz1gWZboDeBIiq626uM7hxe38IxLem_tWUdA5ynyXdtv_U6oeceBiXolCalKPmXGcLV8SOfJvG3sILzCK1VLD2-GTHDJdir';
    $region = 'us-east-1';
    $service = 'execute-api';
    $email = 'nitrouspurchases@gmail.com';

    $options = array();
    $headers = array();
    $host = "na.business-api.amazon.com";
    //.execute-api.us-east-1.amazonaws.com
    //$host = "na.business-api.amazon.com";
    //Or you can define your host here.. I am using API gateway.

    $alg = 'sha256';

    $date = new DateTime('UTC');

    $dd = $date->format('Ymd\THis\Z');

    $amzdate2 = new DateTime('UTC');
    $amzdate2 = $amzdate2->format('Ymd');
    $amzdate = $dd;

    $algorithm = 'AWS4-HMAC-SHA256';


    // $parameters = (array) $obj->data;
    //
    //    if($obj->data == null || empty($obj->data))
    // {
    //     $obj->data = "";
    // }else{
    //     $param = json_encode($obj->data);
    //     if($param == "{}")
    //     {
    //         $param = "";
    //
    //     }

    $requestPayload = strtolower($param);
    $hashedPayload = hash($alg, $requestPayload);

    $canonical_uri = $uri;
    $canonical_querystring = '/B081G4G8N8?productRegion=US&locale=es_US';

    $canonical_headers = "content-type:" . "application/json" . "\n" . "host:" . $host . "\n" . "x-amz-date:" . $amzdate . "\n" . "x-amz-access-token:" . $token . "\n" . "x-amz-user-email:" . $email . "\n";
    $signed_headers = 'host;x-amz-date;x-amz-access-token;x-amz-user-email';
    $canonical_request = "" . $method . "\n" . $canonical_uri . "\n" . $canonical_querystring . "\n" . $canonical_headers . "\n" . $signed_headers . "\n" . $hashedPayload;

    $credential_scope = $amzdate2 . '/' . $region . '/' . $service . '/' . 'aws4_request';
    $string_to_sign  = "" . $algorithm . "\n" . $amzdate . "\n" . $credential_scope . "\n" . hash('sha256', $canonical_request) . "";
    //string_to_sign is the answer..hash('sha256', $canonical_request)//

    $kSecret = 'AWS4' . $secretKey;
    $kDate = hash_hmac($alg, $amzdate2, $kSecret, true);
    $kRegion = hash_hmac($alg, $region, $kDate, true);
    $kService = hash_hmac($alg, $service, $kRegion, true);
    $kSigning = hash_hmac($alg, 'aws4_request', $kService, true);
    $signature = hash_hmac($alg, $string_to_sign, $kSigning);
    $authorization_header = $algorithm . ' ' . 'Credential=' . $access_key . '/' . $credential_scope . ', ' .  'SignedHeaders=' . $signed_headers . ', ' . 'Signature=' . $signature;

    $headers = [
        //    'content-type'=>'application/x-www-form-urlencoded',
        'x-amz-access-token' => $token,
        'x-amz-date' => $amzdate,
        'x-amz-user-email' => 'nitrouspurchases@gmail.com',
        'Authorization' => $authorization_header,
        'host' => 'na.business-api.amazon.com'
    ];
    return $headers;
}

if (!function_exists('jobDispatchFunc')) {
    function jobDispatchFunc($class, $parameters, $queue_type = 'default', $delay = 0)
    {
        $class = 'App\\Jobs\\' . $class;
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            dispatch(new $class($parameters))->onConnection('redis')->onQueue($queue_type)->delay($delay);
        } else {
            dispatch(new $class($parameters))->delay($delay);
        }
    }
}

if (!function_exists('table_model_set')) {
    function table_model_set(string $country_code, string $model, string $table_name): object
    {
        $country_code_lr = strtolower($country_code);

        $namespace = 'App\\Models\\Admin\\BB\\' . $model;
        $product_model = new $namespace;

        $table_name = ($table_name == "product") ? $table_name . '_' . $country_code_lr : $table_name;

        return $product_model->setTable($table_name . 's');
    }
}


if (!function_exists('table_model_create')) {
    function table_model_create(string $country_code, string $model, string $table_name): object
    {
        $country_code_lr = strtolower($country_code);
        $namespace = 'App\\Models\\Catalog\\' . $model;
        $product_model = new $namespace;

        return $product_model->setTable($table_name . $country_code_lr . 's');
    }
}

if (!function_exists('table_model_change')) {
    function table_model_change(string $model_path, string $model_name, string $table_name): object
    {
        $namespace = 'App\\Models\\ShipNTrack\\' . $model_path . '\\' . $model_name;
        $table_model = new $namespace;

        return $table_model->setTable($table_name);
    }
}

if (!function_exists('commandExecFunc')) {
    function commandExecFunc(string $user_command): bool
    {

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && nohup php artisan $user_command > /dev/null &";

            exec($command);
        } else {

            Artisan::call("$user_command");
        }

        return true;
    }
}

if (!function_exists('buyboxCountrycode')) {
    function buyboxCountrycode()
    {
        $source = [
            'AE' => 38,
            'IN' => 39,
            'US' => 40,
            'SA' => 45
        ];

        return $source;
    }
}

if (!function_exists('poundToKg')) {
    function poundToKg($weight)
    {
        $weight_kg = $weight / 2.205;
        return $weight_kg;
    }
}

if (!function_exists('VolumetricIntoKG')) {
    function VolumetricIntoKG($dimension)
    {
        $divisor = getSystemSettingsValue('volumetric_divisor_for_pricing', 6000);
        $cm = $dimension * 16.388;  // convert inch to centimeters.
        $volumetricOfKg = $cm / $divisor; // volumetric in kg.
        return $volumetricOfKg;
    }
}

if (!function_exists('VolumetricIntoPounds')) {
    function VolumetricIntoPounds($dimension)
    {
        $divisor = getSystemSettingsValue('volumetric_divisor_for_pricing', 6000);
        $volumetricOfPounds = $dimension / $divisor;
        return $volumetricOfPounds;
    }
}

if (!function_exists('getWeight')) {

    function getWeight($dimensions)
    {
        $value = (json_decode($dimensions));
        if (isset($value->Weight)) {

            if ($value->Weight->Units == 'pounds') {

                $weight_pound = ($value->Weight->value);
                return round($weight_pound, 2);
            }
        } else {
            return 0.5;
        }
    }
}
if (!function_exists('GetRateChart')) {

    function GetRateChart($source_destination)
    {
        $rate_chart = Ratemaster::where('source_destination', $source_destination)->get();
        $rate_array = [];
        foreach ($rate_chart as  $value) {
            $weight = $value->weight;
            $rate_array[$weight] = [
                'base_rate' => $value->base_rate,
                'lmd_cost' => $value->lmd_cost
            ];
        }
        return $rate_array;
    }
}

if (!function_exists('boe_loop')) {
    function boe_loop($key, $BOE_array, $key_tocheck, &$courier_basic_details, $main_key)
    {
        $name_details = '';
        $check_key = $key + 1;

        while ($BOE_array[$check_key] != $key_tocheck) {
            $name_details .= $BOE_array[$check_key];
            $check_key++;
        }

        $courier_basic_details[$main_key] = $name_details;

        return $courier_basic_details;
    }
}

if (!function_exists('SmsaTrackingResponse')) {

    function SmsaTrackingResponse($awbNo)
    {
        $password = config('database.smsa_password');
        $url = "http://track.smsaexpress.com/SECOM/SMSAwebService.asmx";

        $xmlRequest = "<?xml version='1.0' encoding='utf-8'?>
<soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema'
    xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
    <soap:Body>
        <getTracking xmlns='http://track.smsaexpress.com/secom/'>
            <awbNo>$awbNo</awbNo>
            <passkey>$password</passkey>
        </getTracking>
    </soap:Body>
</soap:Envelope>";

        $headers = array(
            'Content-type: text/xml',
        );

        $ch = curl_init();
        //setting the curl options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);

        $plainXML = mungXML(trim($data));
        $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $arrayResult = $arrayResult['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram'];
        if (array_key_exists('NewDataSet', $arrayResult)) {

            return $arrayResult['NewDataSet']['Tracking'];
        } else {

            // echo "Invalid Awb No. ". $awbNo;
        }
    }
}

if (!function_exists('mungXML')) {
    function mungXML($xml)
    {
        $obj = SimpleXML_Load_String($xml);
        if ($obj === FALSE) return $xml;

        // GET NAMESPACES, IF ANY
        $nss = $obj->getNamespaces(TRUE);
        if (empty($nss)) return $xml;

        // CHANGE ns: INTO ns_
        $nsm = array_keys($nss);
        foreach ($nsm as $key) {
            // A REGULAR EXPRESSION TO MUNG THE XML
            $rgx
                = '#' // REGEX DELIMITER
                . '(' // GROUP PATTERN 1
                . '\<' // LOCATE A LEFT WICKET
                . '/?' // MAYBE FOLLOWED BY A SLASH
                . preg_quote($key) // THE NAMESPACE
                . ')' // END GROUP PATTERN
                . '(' // GROUP PATTERN 2
                . ':{1}' // A COLON (EXACTLY ONE)
                . ')' // END GROUP PATTERN
                . '#' //REGEXDELIMITER
            ;
            // INSERT THE UNDERSCORE INTO THE TAG NAME
            $rep
                = '$1' // BACKREFERENCE TO GROUP 1
                . '_' // LITERAL UNDERSCORE IN PLACE OF GROUP 2
            ;
            // PERFORM THE REPLACEMENT
            $xml = preg_replace($rgx, $rep, $xml);
        }
        return $xml;
    } //End :: mungXML()
}

if (!function_exists('BombinoTrackingResponse')) {
    function BombinoTrackingResponse($awb_no)
    {
        $bombino_account_id = config('database.bombino_account_id');
        $bombino_user_id = config('database.bombino_user_id');
        $bombino_password = config('database.bombino_password');
        $url = "http://api.bombinoexp.in/bombinoapi.svc/Tracking?AccountId=$bombino_account_id&UserId=$bombino_user_id&Password=$bombino_password&AwbNo=$awb_no";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        return $response;
    }
    //
}

if (!function_exists('smsa_tracking')) {
    function smsa_tracking($smsa_awb)
    {
        $tracking_detials = [];
        $smsa_t_details = SmsaTracking::where('awbno', $smsa_awb)->get();
        foreach ($smsa_t_details as $details) {
            // dd($details);
            $tracking_detials[] = [
                'Date_Time' => $details->date,
                'Location' => $details->location,
                'Activity' => $details->activity,
                'forwarder' => 'Smsa'
            ];
        }
        return $tracking_detials;
    }
}

if (!function_exists('bombino_tracking')) {

    function bombino_tracking($bombino_awb)
    {
        $tracking_detials = [];
        $bomino_tracking_details = BombinoTracking::with('bombinoTrackingJoin')->where('awbno', $bombino_awb)->get();

        foreach ($bomino_tracking_details as $details) {

            foreach ($details->bombinoTrackingJoin as $value) {

                $tracking_detials[] = [
                    'Date_Time' => $value->action_date . ' ' . $value->action_time,
                    'Location' => $value->location,
                    'Activity' => $value->exception,
                    'forwarder' => 'Bombino'
                ];
            }
        }
        return $tracking_detials;
    }
}

// if (!function_exists('forwarderTrackingEvent')) {
//     function forwarderTrackingEvent($key)
//     {
//         $array_tables = [
//             [
//                 'Table_name' => 'BombinoTrackingDetails',
//                 'Table_column' => 'exception',
//                 'Model_path' => 'Bombino\\'
//             ],
//             [
//                 'Table_name' => 'SmsaTrackings',
//                 'Table_column' => 'activity',
//                 'Model_path' => 'SMSA\\'
//             ],
//             [],
//             [
//                 'Table_name' => 'AramexTracking',
//                 'Table_column' => 'update_description',
//                 'Model_path' => 'Aramex\\'
//             ],
//         ];

//         $table_name = $array_tables[$key]['Table_name'];
//         $table_column = $array_tables[$key]['Table_column'];
//         $model_path = $array_tables[$key]['Model_path'];

//         $table_model = table_model_change(model_path: $model_path, table_name: $table_name);

//         return [
//             $table_model,
//             $table_column
//         ];
//     }
// }

if (!function_exists('getTrackingDetails')) {
    function getTrackingDetails($awb_no)
    {
        $bombino_t_details = [];
        $smsa_t_detials = [];

        $order = config('database.connections.order.database');
        $order_item = $order . '.orderitemdetails';
        $packet_forwarder = PacketForwarder::where('awb_no', $awb_no)
            ->join($order_item, 'packet_forwarders.order_id', '=', $order_item . '.amazon_order_identifier')
            ->get([
                'packet_forwarders.status',
                'packet_forwarders.forwarder_1',
                'packet_forwarders.forwarder_2',
                'packet_forwarders.forwarder_1_awb',
                'packet_forwarders.forwarder_2_awb',
                $order_item . '.amazon_order_identifier',
                $order_item . '.shipping_address',
            ])
            ->first();

        if (!empty($packet_forwarder)) {

            $forwarder_1 = $packet_forwarder->forwarder_1;
            $forwarder_1_awb = $packet_forwarder->forwarder_1_awb;

            $forwarder_2 = $packet_forwarder->forwarder_2;
            $forwarder_2_awb = $packet_forwarder->forwarder_2_awb;

            if (strtoupper($forwarder_1) == 'BOMBINO') {

                $bombino_t_details = bombino_tracking($forwarder_1_awb);
            } elseif (strtoupper($forwarder_1) == "SMSA") {

                $smsa_t_detials = smsa_tracking($forwarder_1_awb);
            }

            if (strtoupper($forwarder_2) == 'BOMBINO') {

                $bombino_t_details = bombino_tracking($forwarder_2_awb);
            } elseif (strtoupper($forwarder_2_awb) == "SMSA") {

                $smsa_t_detials = smsa_tracking($forwarder_2_awb);
            }

            $tracking_details = [...$bombino_t_details, ...$smsa_t_detials];
            $column = array_column($tracking_details, 'Date_Time');
            array_multisort($column, SORT_DESC, $tracking_details);

            $result = [
                'tracking_details' => $tracking_details,
                'shipping_address' => $packet_forwarder->shipping_address,
            ];

            return $result;
        } else {
            return 'Invalid AWB';
        }
    }
}

if (!function_exists('getSystemSettingsValue')) {
    function getSystemSettingsValue(String $key, $default)
    {
        $records = SystemSetting::where('key', $key)->first();
        $value = isset($records->value) ? $records->value : $default;

        return $value;
    }
}

if (!function_exists('formatInIndianStyle')) {
    function formatInIndianStyle($num)
    {
        // This is my function
        $pos = strpos((string) $num, ".");
        if ($pos === false) {
            $decimalpart = "00";
        } else {
            $decimalpart = substr($num, $pos + 1, 2);
            $num = substr($num, 0, $pos);
        }

        if (strlen($num) > 3 & strlen($num) <= 12) {
            $last3digits = substr($num, -3);
            $numexceptlastdigits = substr($num, 0, -3);
            $formatted = makecomma($numexceptlastdigits);
            $stringtoreturn = $formatted . "," . $last3digits . "." . $decimalpart;
        } elseif (strlen($num) <= 3) {
            $stringtoreturn = $num . "." . $decimalpart;
            $stringtoreturn = $num;
        } elseif (strlen($num) > 12) {
            $stringtoreturn = number_format($num, 0);
        }

        if (substr($stringtoreturn, 0, 2) == "-,") {
            $stringtoreturn = "-" . substr($stringtoreturn, 2);
        }

        return $stringtoreturn;
    }
}

if (!function_exists('trimTrailingZeroes')) {
    function trimTrailingZeroes($nbr)
    {
        return strpos($nbr, '.') !== false ? rtrim(rtrim($nbr, '0'), '.') : $nbr;
    }
}

if (!function_exists('makecomma')) {
    function makecomma($input)
    {
        // This function is written by some anonymous person - I got it from Google
        if (strlen($input) <= 2) {
            return $input;
        }
        $length = substr($input, 0, strlen($input) - 2);
        $formatted_input = makecomma($length) . "," . substr($input, -2);
        return $formatted_input;
    }
}

if (!function_exists('fileManagement')) {
    function fileManagement()
    {
        $file_info = FileManagement::select('id', 'user_id', 'type', 'module', 'file_path', 'command_name', 'header')
            ->where('status', '0')
            ->get()
            ->toArray();

        $ignore = [
            'ASIN_BUYBOX_',
            'ASIN_DESTINATION_',
            'ASIN_SOURCE_',
            'BUYBOX_EXPORT_',
            'CATALOG_PRICE_EXPORT_',
            'CATALOG_EXPORT_',
            'ORDER_',
            'CATALOG_PRICE_EXPORT_ALL_'
        ];

        $file_management_update = '';
        foreach ($file_info as $file_data) {

            $fm_id = $file_data['id'];
            $user_id = $file_data['user_id'];
            $type = $file_data['type'];
            $module = explode('_', str_replace($ignore, '', $file_data['module']));
            $path = $file_data['file_path'];
            $command_name = $file_data['command_name'];
            $header = isset($file_data['header']) ? json_decode($file_data['header'])->data ?? '' : '';
            $destination = isset($module[0]) ? $module[0] : '';
            $priority = isset($module[1]) ? $module[1] : '';

            $file_management_update = FileManagement::find($fm_id);
            $file_management_update->command_start_time = now();
            $file_management_update->status = '1';
            $store_id = $type == 'IMPORT_ORDER' ?  $destination : '';

            $destination = str_replace(',', '_', $destination);

            commandExecFunc("{$command_name} --columns=fm_id={$fm_id},store_id={$store_id},user_id={$user_id},destination={$destination},priority={$priority},path={$path},header={$header}");

            $file_management_update->update();
        }
    }
}

if (!function_exists('fileManagementUpdate')) {

    function fileManagementUpdate($id, $command_end_time = NULL, $status = NULL, $msg = NULL)
    {
        if ($status == NULL) {
            $status = '1';
        }

        $file_management_update_sep = FileManagement::find($id);
        $file_management_update_sep->status = $status;
        $file_management_update_sep->info = $msg;
        $file_management_update_sep->command_end_time = $command_end_time;
        $file_management_update_sep->update();
    }
}

if (!function_exists('fileManagementMonitoring')) {
    function fileManagementMonitoring(String $module_type)
    {
        $file_data = '';
        $type = $module_type;
        $file_management_info =
            FileManagement::select('command_end_time')
            ->where('type', $type)
            ->where('command_end_time', '0000-00-00 00:00:00')
            ->get()
            ->toArray();

        if (count($file_management_info) > 0) {
            $file_data = $file_management_info[0]['command_end_time'];
        }
        return $file_data;
    }
}

if (!function_exists('fileManagementMonitoringNew')) {
    function fileManagementMonitoringNew(String $module_type)
    {
        $file_check =   FileManagement::select('user_id', 'created_at', 'command_end_time', 'info')
            ->where('type', $module_type)
            ->orderBy('id', 'desc')
            ->first();
        if ($file_check) {

            $file_check = $file_check->toArray();

            $user_name = User::where('id', $file_check['user_id'])->get('name')->toArray();

            $user_name = $user_name[0]['name'] ?? '';
            $created_at = date('d-m-Y h:i:s', strtotime($file_check['created_at']));
            $info = $file_check['info'];

            $html_txt = '';
            $status = '';

            if ($file_check['command_end_time'] == '0000-00-00 00:00:00') {
                $status = 'Processing';
                if (str_contains($module_type, 'EXPORT')) {

                    $html_txt = "Previous file export is still processing 
                    <br>
                        Exported By: $user_name
                    <br>
                        Export Time: $created_at 
                    <br>
                        Status: Processing 
                    <br>";
                } elseif (str_contains($module_type, 'IMPORT')) {

                    $html_txt = "Previous Uploaded file is still processing 
                    <br>
                        Uploaded By: $user_name
                    <br>
                         Uploaded Time: $created_at 
                    <br>
                        Status: Processing 
                    <br>";
                }
            } else if ($info != '') {

                $html_txt = "Previous uploaded file has error 
            <br>
                Uploaded By: $user_name
            <br>
              Uploaded Time: $created_at<br>
             <br>
                Status: Failed 
            <br>
                 Remark: $info";
            }

            return [
                'status' => $status,
                'description' => $html_txt
            ];
        } else {

            return [
                'status' => '',
                'description' => ''
            ];
        }
    }
}

if (!function_exists('CSV_Reader')) {
    function CSV_Reader(string $file_path, string $delimiter = ','): object
    {
        if (!Storage::exists($file_path)) {
            return false;
        }

        $reader = Reader::createFromPath(Storage::path($file_path), 'r');
        $reader->setDelimiter($delimiter);
        $reader->setHeaderOffset(0);

        return $reader->getRecords();
    }
}


if (!function_exists('CSV_Write')) {
    function CSV_Write($file_path, $columnsHeader, $records = null)
    {

        $CSV_Writer = null;

        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }

        if (Storage::exists($file_path)) {

            $CSV_Writer = Writer::createFromPath(Storage::path($file_path), "w");
            $CSV_Writer->insertOne($columnsHeader);

            if ($records) {
                // Log::emergency('Writig Here ');
                $CSV_Writer->insertAll($records);
            }
        }

        return $CSV_Writer;
    }
}


if (!function_exists('CSV_w')) {
    function CSV_w($file_name, $record, $headers = []): void
    {
        $writer = (object)'';

        if (!Storage::exists($file_name)) {

            $writer = CSV_Write($file_name, $headers, $record);
        } else {

            $writer = Writer::createFromPath(Storage::path($file_name), "a");
            $writer->insertaLL($record);
        }
    }
}

if (!function_exists('ProcessManagementUpdate')) {
    function ProcessManagementUpdate($pm_id, $command_end_time)
    {
        $process_management_update = ProcessManagement::find($pm_id);
        $process_management_update->status = '1';
        $process_management_update->command_end_time = $command_end_time;
        $process_management_update->update();
    }
}

if (!function_exists('CacheForCommandScheduler')) {
    function CacheForCommandScheduler()
    {
        cache()->rememberForever('Schedule_command', function () {
            return CommandScheduler::where('status', '1')->get();
        });
    }
}

if (!function_exists('aws_merchant_ids')) {
    function aws_merchant_ids()
    {
        if (Cache::has("aws_merchant_ids")) {
            return Cache::get("aws_merchant_ids");
        }

        $get_datas = [];
        $aws_merchant_ids = Aws_credential::select('seller_id', 'merchant_id')->where("merchant_id", "!=", "Patch")->get()->toArray();

        foreach ($aws_merchant_ids as $aws_merchant_id) {
            $get_datas[$aws_merchant_id['seller_id']] = $aws_merchant_id['merchant_id'];
        }

        Cache::set("aws_merchant_ids", $get_datas);

        return $aws_merchant_ids;
    }
}

if (!function_exists('ZipFileConverter')) {
    function ZipFileConverter($zipPath, $totalFile, $filePath): void
    {
        $zip = new ZipArchive;
        $file_path = Storage::path($zipPath);
        if (!Storage::exists($zipPath)) {
            Storage::put($zipPath, '');
        }
        if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
            foreach ($totalFile as $value) {

                $path = Storage::path($filePath . "/" . $value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}

if (!function_exists('DeleteFileFromFolder')) {
    function DeleteFileFromFolder($folderName, $countryCode, $priority)
    {
        $files = glob(Storage::path('excel/downloads/' . $folderName . '/' . $countryCode . '/' . $priority . '/*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

if (!function_exists('ServicesClass')) {
    function ServicesClass(string $services_path, string $services_class): object
    {
        $namespace = 'App\\Services\\' . $services_path . '\\' . $services_class;
        $services = new $namespace;

        return $services;
    }
}

if (!function_exists("country_code_region_wise")) {

    function country_code_region_wise(): array
    {

        return [
            'FE' => ["SG", "AU", "JP"],
            'NA' => ["BR", "CA", "MX", "US"],
            'EU' => ["AE", "DE", "EG", "ES", "FR", "UK",  "IN", "IT", "NL", "PL", "SA", "SE", "TR"]
        ];
    }
}

if (!function_exists("indexrebuild")) {

function indexrebuild($pid, $site)
{
 $domains = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site])->pluck('domain')->ToArray();

            foreach ($domains as $domain) {
                if ($domain == 'attribute') {

                    $attribute_lists = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site, 'domain' => $domain])->pluck('type', 'refid')->ToArray();

                    foreach ($attribute_lists as $key => $attribute_list) {

                        $attributes = DB::connection('cliqnshop')->table('mshop_attribute')->where(['id' => $key, 'siteid' => $site])->pluck('type', 'code')->ToArray();

                        foreach ($attributes as $key1 => $attribute) {

                            $index_attribute = [
                                'prodid' => $pid,
                                'siteid' => $site,
                                'artid' => $pid,
                                'attrid' => $key,
                                'listtype' => $attribute_list, // type from mshop_product_list
                                'type' => $attribute,
                                'code' => $key1,
                                'mtime' => now(),
                            ];
                            DB::connection('cliqnshop')->table('mshop_index_attribute')->upsert(
                                $index_attribute,
                                ['unq_msindat_p_s_aid_lt'],
                                ['prodid', 'siteid', 'artid', 'attrid', 'listtype', 'type', 'code', 'mtime']
                            );
                        }
                    }
                }

                if ($domain == 'catalog') {
                    $catalog_lists = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site, 'domain' => $domain])->select('type', 'refid', 'pos')->get()->ToArray();

                    $index_catalog = [
                        'prodid' => $pid,
                        'siteid' => $site,
                        'catid' => $catalog_lists[0]->refid,
                        'listtype' => $catalog_lists[0]->type, // type from mshop_product_list
                        'pos' => $catalog_lists[0]->pos, //from mshop_product_list
                        'mtime' => now(),
                    ];
                    DB::connection('cliqnshop')->table('mshop_index_catalog')->upsert(
                        $index_catalog,
                        ['unq_msindca_p_s_cid_lt_po'],
                        ['prodid', 'siteid', 'catid', 'listtype', 'pos', 'mtime']
                    );
                }

                if ($domain == 'keyword') {
                    $keyword_lists = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site, 'domain' => $domain])->pluck('refid')->ToArray();

                    foreach ($keyword_lists as $keyword_list) {
                        $index_generic_key = [
                            'prodid' => $pid,
                            'siteid' => $site,
                            'keyid' => $keyword_list,
                            'mtime' => now(),
                        ];
                        DB::connection('cliqnshop')->table('mshop_index_keyword')->upsert(
                            $index_generic_key,
                            ['unq_msindkey_pid_kid_sid'],
                            ['keyid', 'mtime']
                        );
                    }
                }

                if ($domain == 'price') {
                    $price_list = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site, 'domain' => $domain])->pluck('refid')->ToArray();

                    $price = DB::connection('cliqnshop')->table('mshop_price')->where(['id' => $price_list[0], 'siteid' => $site])->select('currencyid', 'value')->get()->ToArray();

                    $index_price = [
                        'prodid' => $pid,
                        'siteid' => $site,
                        'currencyid' => $price[0]->currencyid,
                        'value' => $price[0]->value,
                        'mtime' => now(),
                    ];
                    DB::connection('cliqnshop')->table('mshop_index_price')->upsert(
                        $index_price,
                        ['unq_msindpr_pid_sid_cid'],
                        ['prodid', 'siteid', 'currencyid', 'value', 'mtime']
                    );
                }

                if ($domain == 'supplier') {
                    $supplier_list = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site, 'domain' => $domain])->select('type', 'refid', 'pos')->get()->ToArray();

                    $index_supplier = [
                        'prodid' => $pid,
                        'siteid' => $site,
                        'supid' => $supplier_list[0]->refid,
                        'listtype' => $supplier_list[0]->type,
                        'latitude' => null,
                        'longitude' => null,
                        'pos' => $supplier_list[0]->pos,
                        'mtime' => now(),
                    ];

                    DB::connection('cliqnshop')->table('mshop_index_supplier')->upsert(
                        $index_supplier,
                        ['unq_msindsu_p_s_lt_si_po_la_lo'],
                        ['prodid', 'siteid', 'supid', 'listtype', 'pos', 'mtime']
                    );
                }

                if ($domain == 'text') {
                    $text_list = DB::connection('cliqnshop')->table('mshop_product')->where(['id' => $pid, 'siteid' => $site])->select('code', 'label', 'url')->get()->ToArray();

                    $index_text = [
                        'prodid' => $pid,
                        'siteid' => $site,
                        'langid' => 'en',
                        'url' => $text_list[0]->url,
                        'name' => $text_list[0]->label,
                        'content' => mb_strtolower($text_list[0]->code) . ' ' . mb_strtolower($text_list[0]->label),
                        'mtime' => now(),
                    ];
                    DB::connection('cliqnshop')->table('mshop_index_text')->upsert(
                        $index_text,
                        ['unq_msindte_pid_sid_lid_url'],
                        ['prodid', 'siteid', 'url', 'name', 'content', 'mtime']
                    );

                }
            }
            }
}