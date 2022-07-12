<?php

namespace App\Services\AWS_Business_API\AWS_POC;


class ProductsRequest
{

    public function getASINpr()
    {
        date_default_timezone_set('Asia/Jakarta');

        // require_once('refrashToken.php');
        $client_id = "amzn1.application-oa2-client.6c64a78c8f214ae1999ba6725aa68bd5";
        $client_secret = "80b1db8f2e3ae4b755bd50a0bcc21228694381e6a35b178efdb43799ccedd1ae";
        $refresh_token =
            "Atza|IwEBIHEVMfJBrO4xxiQJrxV0wdEMs6yCeAkOalPFT7OurFLFk0WcckjytvbWXZw3AV1lpANE7IV8pQZ8d6uYh6roujC6AwkuWWafFqUMWIEWsijV16Bqa0Nd3BVHI8lSPJboQ7R3bH8A_2eh5oN1yJQLYpQicvGikXtVtA07pO5SUT31IxTRMT5JB1ja7EJ-vzZk_hZZQN3Zx7d1ECxsKCPlOONBLUOHt_5zhKhae7USTmNVHJSm4cjqx5VYlOdYxeCY_CN7yTE7im5tFWCOAKVBmiogOcrsdm4_LzXNwBQhNTLL4rStEoIYLaD19G-cC5GkmlFhcPOg0uKlInHLRe5JUgaH";
        $request_data = array(
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "refresh_token" => $refresh_token,
            "grant_type" => "refresh_token"
        );

        $reqToken =
            "Atza|IwEBIHEVMfJBrO4xxiQJrxV0wdEMs6yCeAkOalPFT7OurFLFk0WcckjytvbWXZw3AV1lpANE7IV8pQZ8d6uYh6roujC6AwkuWWafFqUMWIEWsijV16Bqa0Nd3BVHI8lSPJboQ7R3bH8A_2eh5oN1yJQLYpQicvGikXtVtA07pO5SUT31IxTRMT5JB1ja7EJ-vzZk_hZZQN3Zx7d1ECxsKCPlOONBLUOHt_5zhKhae7USTmNVHJSm4cjqx5VYlOdYxeCY_CN7yTE7im5tFWCOAKVBmiogOcrsdm4_LzXNwBQhNTLL4rStEoIYLaD19G-cC5GkmlFhcPOg0uKlInHLRe5JUgaH";
        //  $reqToken = getToken($request_data);

        $host               = "na.business-api.amazon.com";
        $accessKey          = "AKIARVGPJZCJHLW5MH63";
        $secretKey          = "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";
        $region             = "us-east-1";
        $service            = "execute-api";
        $requestUrl         = "https://na.business-api.amazon.com/products/2020-08-26/products/B07NQPLWXW";
        $uri                = '/products/2020-08-26/products/B07NQPLWXW';
        $httpRequestMethod  = 'GET';
        $data                = '';

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
            $debug = TRUE
        ) {

            $terminationString  = 'aws4_request';
            $algorithm      = 'AWS4-HMAC-SHA256';
            $phpAlgorithm       = 'sha256';
            $canonicalURI       = $uri;
            $canonicalQueryString   = 'facets=OFFERS&locale=en_US&productRegion=US';
            $signedHeaders      = 'host;x-amz-access-token;x-amz-date;x-amz-user-email';
            $userEmail     = "nitrouspurchases@gmail.com";

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
            // if ($debug) {
            //     echo "<h5>Canonical to string</h5>";
            //     echo "<pre>";
            //     echo $requestCanonicalRequest;
            //     echo "</pre>";
            // }

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
            if ($debug) {
                // echo "<h5>String to Sign</h5>";
                // echo "<pre>";
                // echo $stringToSignStr;
                // echo "</pre>";
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
            $debug = true
        );
        // echo '<pre>';
        // print_r($AwsSignature);
        $curl = curl_init();

        $headersFS = array(
            'x-amz-access-token:' . $reqToken,
            'host:na.business-api.amazon.com',
            $AwsSignature[0],
            'x-amz-date:' . $Time,
            'x-amz-user-email:nitrouspurchases@gmail.com',
        );

        // echo '<pre>';
        // print_r($headersFS);

        curl_setopt($curl, CURLOPT_URL, "https://na.business-api.amazon.com/products/2020-08-26/products/B07NQPLWXW?facets=OFFERS&locale=en_US&productRegion=US");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headersFS);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        $server_APIoutput = curl_exec($curl);
        $JsonResponsepr = json_decode($server_APIoutput);

        return $JsonResponsepr;

        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        }

        curl_close($curl);
    }
}
