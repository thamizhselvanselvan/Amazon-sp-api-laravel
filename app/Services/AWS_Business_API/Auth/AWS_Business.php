<?php

namespace App\Services\AWS_Business_API\Auth;

use DateTime;
use DatePeriod;

class AWS_Business {

    public function sign($host, $uri, $requestUrl,
                $accessKey, $secretKey, $region, $service,
                $httpRequestMethod, $data, $debug = TRUE) {

              $reqEmail = 'nitrouspurchases@gmail.com';
              $terminationString  = 'aws4_request';
              $algorithm      = 'AWS4-HMAC-SHA256';
              $phpAlgorithm       = 'sha256';
              $canonicalURI       = $uri;
              $canonicalQueryString   = 'productRegion=US&locale=es_US';
              $signedHeaders      = 'content-type;host;x-amz-access-token;x-amz-date;x-amz-user-email';
              $reqToken = 'Atza|IwEBIGVsmmnXo9b3xSuoeypSSlvQtk37QrrHmabOhogciCW_fEng9G4qxZrvROLTuEOfL8IOWMuYKFp1JlVFH9uQE_0DsyPbVFZSlg4zqsFfEIncmABIL8iSlaHJbzobNiNgifWfzOOqs_nvQSswC6I76qZdDxtsj77KqxX_NJ2kRNe76TnpoECWO7EeSNndk-tsPQZ1Rc-BLHXr09CgXczvZvPXTHHA51wlBbEP3UhPvrM0PY_x0NBSlBOmN7Aipe-z27NNAV8CT7I8I-g66SSUhEIwBdqiWHlqrDrYch_dhPbYnGlrhvUmBSvfF9i3UdTHMWsuHVkTc8IY8o33zU6peEor';

              $currentDateTime = new DateTime('UTC');
              $reqDate = $currentDateTime->format('Ymd');
              $reqDateTime = $currentDateTime->format('Ymd\THis\Z');

              // Create signing key
              $kSecret = 'AWS4' . $secretKey;
              $kDate = hash_hmac($phpAlgorithm, $reqDate,  $kSecret, true);
              $kRegion = hash_hmac($phpAlgorithm, $region, $kDate, true);
              $kService = hash_hmac($phpAlgorithm, $service, $kRegion, true);
              $kSigning = hash_hmac($phpAlgorithm, $terminationString, $kService, true);

              // Create canonical headers
              $canonicalHeaders = array();
              $canonicalHeaders[] = 'content-type:application/x-www-form-urlencoded; charset=utf-8';
              $canonicalHeaders[] = 'host:' . $host;
              $canonicalHeaders[] = 'x-amz-access-token:' . $reqToken;
              $canonicalHeaders[] = 'x-amz-date:' . $reqDateTime;
              $canonicalHeaders[] = 'x-amz-user-email:' . $reqEmail;
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
              $signature = trim(hash_hmac($phpAlgorithm, $stringToSignStr, $kSigning));

              // Create authorization header
              $authorizationHeader = array();
              $authorizationHeader[] = 'Credential=' . $accessKey . '/' . $credentialScopeStr;
              $authorizationHeader[] = 'SignedHeaders=' . $signedHeaders;
              $authorizationHeader[] = 'Signature=' . ($signature);
              $authorizationHeaderStr = $algorithm . ' ' . implode(', ', $authorizationHeader);

              // Request headers
              $headers = array();
              $headers[] = 'Authorization:'.$authorizationHeaderStr;
              // $headers[] = 'content-length:'.strlen($data);
              $headers[] = 'content-type: application/x-www-form-urlencoded; charset=utf-8';
              $headers[] = 'host: ' . $host;
              $headers[] = 'x-amz-access-token: ' . $reqToken;
              $headers[] = 'X-Amz-Date: ' . $reqDateTime;
              $headers[] = 'x-amz-user-email: '.$reqEmail;

              return $headers;
    }


    public function signTest($asin = 'B081G4G8N8', $cano = 'products/2020-08-26/products/', $queryString = 'productRegion=US&locale=es_US') {

      $host = 'na.business-api.amazon.com';
      $service = 'execute-api';
      $region = 'us-east-1';

      $accessKey = "AKIARVGPJZCJHLW5MH63";
      $secretKey = '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';
      $accessToken = 'Atza|IwEBIBmeBZ3mcWW8RekHjmTsLuHnenrYAwcbZiC-DoHLWDV-i1k7JC8qrJY50HANhHSoBB0_AtbBTDdQZx47ben7Hf031KAJe2b5FCmLtqlVsb3cakXlV3knVdq20EsmMsK3CWOh55EJbN6sU2BckIJKjih2eZmf04jpCooPl5JH2sWVsmNzhBcXza8q_cZwNjUeS0lPY5aMMxlLqAdBYLQG5ycXIdzKUEimEq57DeA2QV9neMsO-i_Xzk3HZpz1gWZboDeBIiq626uM7hxe38IxLem_tWUdA5ynyXdtv_U6oeceBiXolCalKPmXGcLV8SOfJvG3sILzCK1VLD2-GTHDJdir';

      $requestUrl = 'https://na.business-api.amazon.com';
      $httpRequestMethod = 'GET';
      $terminationString = 'aws4_request';
      $algorithm = 'AWS4-HMAC-SHA256';
      $phpAlgorithm = 'sha256';
      $signedHeaders = 'content-type;host;x-amz-access-token;x-amz-date';
      $canonicalURI = "$cano";
      $canonicalQueryString   = $queryString;
      $requestHasedPayload = hash($phpAlgorithm, '');

      $currentDateTime = new DateTime('UTC');
      $reqDate = $currentDateTime->format('Ymd');
      $reqDateTime = $currentDateTime->format('Ymd\THis\Z');


      // Create canonical request
      $canonicalRequest = array();
      $canonicalRequest[] = $httpRequestMethod;
      $canonicalRequest[] = $canonicalURI;
      $canonicalRequest[] = 'content-type:application/json';
      $canonicalRequest[] = 'host:na.business-api.amazon.com';
      $canonicalRequest[] = 'x-amz-access-token:' . $accessToken;
      $canonicalRequest[] = 'x-amz-date:' . $reqDateTime . "\n";
      $canonicalRequest[] = rawurlencode($signedHeaders);
      $canonicalRequest[] = $canonicalQueryString;
      $canonicalRequest[] = $requestHasedPayload;
      $requestCanonicalRequest = implode("\n", $canonicalRequest);
      $requestHasedCanonicalRequest = hash($phpAlgorithm, utf8_encode($requestCanonicalRequest));

      // Create signing key
      $kSecret = $secretKey;
      $kDate = hash_hmac($phpAlgorithm, $reqDate, 'AWS4' . $kSecret, true);
      $kRegion = hash_hmac($phpAlgorithm, $region, $kDate, true);
      $kService = hash_hmac($phpAlgorithm, $service, $kRegion, true);
      $kSigning = hash_hmac($phpAlgorithm, $terminationString, $kService, true);


      // Create scope
      $credentialScope = array();
      $credentialScope[] = $reqDate;
      $credentialScope[] = $region;
      $credentialScope[] = $service;
      $credentialScope[] = $terminationString;
      $credentialScopeStr = implode('/', $credentialScope);

      // Create string to sign
      $stringToSign = array();
      $stringToSign[] = $algorithm;
      $stringToSign[] = $reqDateTime;
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
      $headers[] = 'authorization:'. $authorizationHeaderStr;
      $headers[] = 'content-type: application/json';
      $headers[] = 'host: ' . $host;
      $headers[] = 'x-amz-date: ' . $reqDateTime;


     $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $requestUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_POST => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $httpRequestMethod,
        CURLOPT_VERBOSE => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HEADER => false,
        CURLINFO_HEADER_OUT=>true,
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);



    curl_close($curl);

    if ($err) {

            echo "<h5>Error:" . $responseCode . "</h5>";
            echo "<pre>";
            echo $err;
            echo "</pre>";

    } else {

            echo "<h5>Response:" . $responseCode . "</h5>";
            echo "<pre>";
            echo $response;
            echo "</pre>";

    }

    }
}
