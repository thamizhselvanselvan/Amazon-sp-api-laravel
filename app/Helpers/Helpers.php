<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Models\Aws_credential;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

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

if(!function_exists('dateTimeFilter')) {
  function dateTimeFilter($data, $subDays = 6) {

      $period = new DatePeriod(Carbon::now()->subDays($subDays), CarbonInterval::day(), Carbon::today()->endOfDay());

      return array_reverse(array_map(function ($datePeriod) use ($data) {
          $date = $datePeriod->format('d-m-Y');
          return (isset($data[$date])) ? $data[$date] : 0;
      }, iterator_to_array($period)));
  }
}

if(!function_exists('dateTimeFiltermonthly')) {
    function dateTimeFiltermonthly($data, $subDays = 30) {
  
        $period = new DatePeriod(Carbon::now()->subDays($subDays), CarbonInterval::day(), Carbon::today()->endOfDay());
  
        return array_reverse(array_map(function ($datePeriod) use ($data) {
            $date = $datePeriod->format('d-m-Y');
            return (isset($data[$date])) ? $data[$date] : 0;
        }, iterator_to_array($period)));
    }
  }

function calcualteAwsSignatureAndReturnHeaders($host, $uri, $requestUrl,
            $accessKey, $secretKey, $region, $service,
            $httpRequestMethod, $data, $debug = TRUE){

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
    if($debug){
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
    if($debug){
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
    $headers[] = 'authorization: '.$authorizationHeaderStr;
//    $headers[] = 'content-length:'.strlen($data);
    $headers[] = 'content-type: application/x-www-form-urlencoded';
    $headers[] = 'host: ' . $host;
    $headers[] = 'x-amz-date: ' . $reqDateTime;
    $headers[] = 'x-amz-user-email: nitrouspurchases@gmail.com';
    $headers[] = 'x-amz-access-token: '.$accessToken;

    return $headers;
}

function apiCall($header) {

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
function callToAPI($requestUrl, $httpRequestMethod, $headers, $data, $debug=TRUE)
{

  $response = Http::withHeaders($headers)->get($requestUrl);

    if($response->successful()) {
      echo "OK";
    } else {
      dd($headers, $response->body(), $response->headers(), $response->object());
    }

}// End callToAPI




//$host = "na.business-api.amazon.com";
//$requestUrl = "https://na.business-api.amazon.com";


 function signRequest(){
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

    $options = array(); $headers = array();
    $host = "na.business-api.amazon.com";
    //.execute-api.us-east-1.amazonaws.com
    //$host = "na.business-api.amazon.com";
      //Or you can define your host here.. I am using API gateway.

    $alg = 'sha256';

    $date = new DateTime( 'UTC' );

    $dd = $date->format( 'Ymd\THis\Z' );

    $amzdate2 = new DateTime( 'UTC' );
    $amzdate2 = $amzdate2->format( 'Ymd' );
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

    $canonical_headers = "content-type:"."application/json"."\n"."host:".$host."\n"."x-amz-date:".$amzdate."\n"."x-amz-access-token:".$token."\n"."x-amz-user-email:".$email."\n";
    $signed_headers = 'host;x-amz-date;x-amz-access-token;x-amz-user-email';
    $canonical_request = "".$method."\n".$canonical_uri."\n".$canonical_querystring."\n".$canonical_headers."\n".$signed_headers."\n".$hashedPayload;

    $credential_scope = $amzdate2 . '/' . $region . '/' . $service . '/' . 'aws4_request';
    $string_to_sign  = "".$algorithm."\n".$amzdate ."\n".$credential_scope."\n".hash('sha256', $canonical_request)."";
   //string_to_sign is the answer..hash('sha256', $canonical_request)//

    $kSecret = 'AWS4' . $secretKey;
    $kDate = hash_hmac( $alg, $amzdate2, $kSecret, true );
    $kRegion = hash_hmac( $alg, $region, $kDate, true );
    $kService = hash_hmac( $alg, $service, $kRegion, true );
    $kSigning = hash_hmac( $alg, 'aws4_request', $kService, true );
    $signature = hash_hmac( $alg, $string_to_sign, $kSigning );
    $authorization_header = $algorithm . ' ' . 'Credential=' . $access_key . '/' . $credential_scope . ', ' .  'SignedHeaders=' . $signed_headers . ', ' . 'Signature=' . $signature;

    $headers = [
            //    'content-type'=>'application/x-www-form-urlencoded',
                'x-amz-access-token'=>$token,
                'x-amz-date'=>$amzdate,
                'x-amz-user-email' => 'nitrouspurchases@gmail.com',
                'Authorization'=>$authorization_header,
                'host' => 'na.business-api.amazon.com'
              ];
    return $headers;

}

