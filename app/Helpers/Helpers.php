<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Models\Aws_credential;
use PhpParser\Node\Expr\Eval_;
use App\Models\Catalog\Catalog;
use App\Models\Admin\Ratemaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use App\Models\ShipNTrack\Bombino\BombinoTracking;
use App\Models\ShipNTrack\Bombino\BombinoTrackingDetails;


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
        return $originalAmount + ($percentageChange / 100) * $originalAmount;
    }
}

if (!function_exists('removePercentage')) {
    function removePercentage($originalAmount, $percentageChange)
    {
        return $originalAmount - ($percentageChange / 100) * $originalAmount;
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
    function slack_notification($title, $message)
    {
        $webhook = config('pms.PMS_SLACK_NOTIFICATION_WEBHOOK');

        if (empty($webhook)) {
            throw new Exception("Please update your ENV with PMS_SLACK slack webhook url", 1);
        } else {
            //Notification::route('slack', $webhook)->notify(new SlackMessages($title, $message));
        }
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
                slack_notification("Health Check", $msg);
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
        print("Time elapsed: $time_elapsed_secs");

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
    function jobDispatchFunc($class, $parameters, $queue_type = 'default')
    {
        $class = 'App\\Jobs\\' . $class;
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            dispatch(new $class($parameters))->onConnection('redis')->onQueue($queue_type);
        } else {
            dispatch(new $class($parameters));
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
    function table_model_change(string $model_path, string $table_name): object
    {
        $namespace = 'App\\Models\\ShipNTrack\\' . $model_path . $table_name;
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
            'US' => 40
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
                . ')' // ENDGROUP PATTERN 
                . '(' // GROUP PATTERN 2 
                . ':{1}' // A COLON (EXACTLY ONE) 
                . ')' // END GROUP PATTERN 
                . '#' // REGEXDELIMITER 
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
        $smsa_t_details = SmsaTrackings::where('awbno', $smsa_awb)->get();
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

if (!function_exists('forwarderTrackingEvent')) {
    function forwarderTrackingEvent($key)
    {
        $array_tables = [
            [
                'Table_name' => 'BombinoTrackingDetails',
                'Table_column' => 'exception',
                'Model_path' => 'Bombino\\'
            ],
            [
                'Table_name' => 'SmsaTrackings',
                'Table_column' => 'activity',
                'Model_path' => 'SMSA\\'
            ],
        ];

        $table_name = $array_tables[$key]['Table_name'];
        $table_column = $array_tables[$key]['Table_column'];
        $model_path = $array_tables[$key]['Model_path'];

        $table_model = table_model_change(model_path: $model_path, table_name: $table_name);

        return [
            $table_model,
            $table_column
        ];
    }
}

if (!function_exists('getTrackingDetails')) {
    function getTrackingDetails($awb_no)
    {
        $bombino_t_details  = [];
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

            $result  = [
                'tracking_details'  => $tracking_details,
                'shipping_address'  => $packet_forwarder->shipping_address,
            ];

            return $result;
        } else {
            return 'Invalid AWB';
        }
    }
}
