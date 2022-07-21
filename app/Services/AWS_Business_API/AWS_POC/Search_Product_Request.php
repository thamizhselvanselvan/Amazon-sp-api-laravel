<?php

namespace App\Services\AWS_Business_API\AWS_POC;

use Illuminate\Support\Facades\Storage;


class Search_Product_Request
{

    public function getASIN()
    {
        date_default_timezone_set('Asia/Jakarta');

        // require_once('refrashToken.php');
        $client_id = "amzn1.application-oa2-client.6c64a78c8f214ae1999ba6725aa68bd5";
        $client_secret = "80b1db8f2e3ae4b755bd50a0bcc21228694381e6a35b178efdb43799ccedd1ae";
        $refresh_token =
        file_get_contents(Storage::path('Business/token.txt'));
        $request_data = array(
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "refresh_token" => $refresh_token,
            "grant_type" => "refresh_token"
        );

        $reqToken =
        file_get_contents(Storage::path('Business/token.txt'));
        // $reqToken = getToken($request_data);

        $host               = "na.business-api.amazon.com";
        $accessKey          = "AKIARVGPJZCJHLW5MH63";
        $secretKey          = "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";
        $region             = "us-east-1";
        $service            = "execute-api";
        $requestUrl         = "https://na.business-api.amazon.com/products/2020-08-26/products";
        $uri                = "/products/2020-08-26/products";
        $httpRequestMethod  = 'GET';
        $data               =  '';
        $Request_url_query_string = "?productRegion=US&locale=en_US";
        $keyword = "&keywords=Lenovo";
        $canonicalQueryString_custom = 'productRegion=US&locale=en_US';

        if(isset($keyword) && !empty($keyword)) {
          // $Request_url_query_string .= $keyword;
         //   $canonicalQueryString_custom .= $keyword;
        }

       // $Request_url_query_string = urlencode($Request_url_query_string);
       // $canonicalQueryString_custom = urlencode($canonicalQueryString_custom);

        function calcualteAwsSignatureAndReturnHeaders(
            $today,
            $reqToken,
            $host,
            $uri,
            $requestUrl,
            $accessKey,
            $secretKey,
            $region,
            $service,
            $httpRequestMethod,
            $data,
            $canonicalQueryString_custom = '',
            $debug = TRUE
        ) {

            $terminationString  = 'aws4_request';
            $algorithm      = 'AWS4-HMAC-SHA256';
            $phpAlgorithm       = 'sha256';
            $canonicalURI       = $uri;
            $canonicalQueryString   = $canonicalQueryString_custom;
            $signedHeaders      = 'host;x-amz-access-token;x-amz-date;x-amz-user-email';
            $userEmail     = "nitrouspurchases@gmail.com";

            //dd($canonicalQueryString);

            //AMZ date format
            $reqDate = date("Ymd");
            $reqDateTime = date("Ymd\THis\Z");

            $kSecret = $secretKey;
            $kDate = hash_hmac($phpAlgorithm, $reqDate, 'AWS4' . $kSecret, true);
            $kRegion = hash_hmac($phpAlgorithm, $region, $kDate, true);
            $kService = hash_hmac($phpAlgorithm, $service, $kRegion, true);
            $kSigning = hash_hmac($phpAlgorithm, $terminationString, $kService, true);

            // Create canonical headers
            $canonicalHeaders = array();
            $canonicalHeaders[] = 'host:' . $host;
            $canonicalHeaders[] = 'x-amz-access-token:' . $reqToken;
            $canonicalHeaders[] = 'x-amz-date:' . $today;
            $canonicalHeaders[] = 'x-amz-user-email:' . $userEmail;
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
            $stringToSign[] = $today;
            $stringToSign[] = $credentialScopeStr;
            $stringToSign[] = $requestHasedCanonicalRequest;
            $stringToSignStr = implode("\n", $stringToSign);

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
            $headers[] = 'Authorization:' . $authorizationHeaderStr;
            $headers[] = 'host: ' . $host;
            $headers[] = 'x-amz-date: ' . $today;
            $headers[] = 'x-amz-user-email:' . $userEmail;
            $headers[] = 'x-amz-access-token: ' . $reqToken;

            return $headers;
        }

        $dt = date("Y-m-d H:i:s");
        $today = date("Ymd\THis\Z");

        $Time = date("Ymd\THis\Z", strtotime('-7 hours', strtotime($dt)));
        
        $AwsSignature = calcualteAwsSignatureAndReturnHeaders(
            $Time,
            $reqToken,
            $host,
            $uri,
            $requestUrl,
            $accessKey,
            $secretKey,
            $region,
            $service,
            $httpRequestMethod,
            $data,
            $canonicalQueryString_custom,
            $debug = true
        );
       

        $curl = curl_init();

        $headersFS = array(
            'x-amz-access-token:' . $reqToken,
            'host:na.business-api.amazon.com',
            $AwsSignature[0],
            'x-amz-date:' . $Time,
            'x-amz-user-email:nitrouspurchases@gmail.com',
        );  

        curl_setopt($curl, CURLOPT_URL, $requestUrl. $Request_url_query_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headersFS);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        $server_APIoutput = curl_exec($curl);

        dd($server_APIoutput);
        $JsonResponse = json_decode($server_APIoutput);

        return $JsonResponse;

        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        }

        curl_close($curl);
    }
}
