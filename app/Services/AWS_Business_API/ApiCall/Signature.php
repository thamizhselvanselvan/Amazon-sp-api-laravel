<?php

namespace App\Services;


class Signature {

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
      $canonicalQueryString   = 'locale=en_US&productRegion=US';
      $signedHeaders      = 'host;x-amz-access-token;x-amz-date;x-amz-user-email';
      $userEmail     = "nitrouspurchases@gmail.com";
      //AMZ date format
      $reqDate = date("Ymd");
      $reqDateTime = date("Ymd\THis\Z");

      $kSecret = $secretKey;

  }

  public function kSigning($phpAlgorithm, $kSecret, $reqDate, $region, $service, $terminationString) {

      $kDate = hash_hmac($phpAlgorithm, $reqDate, 'AWS4' . $kSecret, true);
      $kRegion = hash_hmac($phpAlgorithm, $region, $kDate, true);
      $kService = hash_hmac($phpAlgorithm, $service, $kRegion, true);

      return hash_hmac($phpAlgorithm, $terminationString, $kService, true);
  }

  public function canonicalHeaders($host, $accessToken, $today, $userEmail) {

    $canonicalHeaders[] = 'host:' . $host;
    $canonicalHeaders[] = 'x-amz-access-token:' . $accessToken;
    $canonicalHeaders[] = 'x-amz-date:' . $today;
    $canonicalHeaders[] = 'x-amz-user-email:' . $userEmail;

    return implode("\n", $canonicalHeaders);
  }

  public function requestHasedPayload($phpAlgorithm, $data) {
     return hash($phpAlgorithm, $data);
  }

  public function canonicalRequest($phpAlgorithm, $httpRequestMethod, $canonicalURI, $canonicalQueryString, $canonicalHeadersStr, $signedHeaders, $requestHasedPayload, $canonicalRequest) {

    $canonicalRequest[] = $httpRequestMethod;
    $canonicalRequest[] = $canonicalURI;
    $canonicalRequest[] = $canonicalQueryString;
    $canonicalRequest[] = $canonicalHeadersStr . "\n";
    $canonicalRequest[] = $signedHeaders;
    $canonicalRequest[] = $requestHasedPayload;
    $requestCanonicalRequest = implode("\n", $canonicalRequest);

    return  hash($phpAlgorithm, utf8_encode($requestCanonicalRequest));
  }

  public function credentialScope($reqDate, $region, $service, $terminationString) {

    $credentialScope[] = $reqDate;
    $credentialScope[] = $region;
    $credentialScope[] = $service;
    $credentialScope[] = $terminationString;

    return implode('/', $credentialScope);
  }

  public function stringToSign($algorithm, $today, $credentialScopeStr, $requestHasedCanonicalRequest) {

    $stringToSign[] = $algorithm;
    $stringToSign[] = $today;
    $stringToSign[] = $credentialScopeStr;
    $stringToSign[] = $requestHasedCanonicalRequest;

    return implode("\n", $stringToSign);
  }

  public function signature($phpAlgorithm, $stringToSignStr, $kSigning) {
    return hash_hmac($phpAlgorithm, $stringToSignStr, $kSigning);
  }

  public function authorizationHeader($accessKey, $credentialScopeStr, $signedHeaders, $signature) : string {

      $authorizationHeader[] = 'Credential=' . $accessKey . '/' . $credentialScopeStr;
      $authorizationHeader[] = 'SignedHeaders=' . $signedHeaders;
      $authorizationHeader[] = 'Signature=' . ($signature);

      return $algorithm . ' ' . implode(', ', $authorizationHeader);
  }

  public function requestHeaders($authorizationHeaderStr, $this->host, $this->today, $userEmail, $accessToken) {

    $headers[] = 'Authorization:' . $authorizationHeaderStr;
    $headers[] = 'host: ' . $host;
    $headers[] = 'x-amz-date: ' . $today;
    $headers[] = 'x-amz-user-email:' . $userEmail;
    $headers[] = 'x-amz-access-token: ' . $accessToken;

    return $headers;
  }

}
