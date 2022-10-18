<?php

namespace App\Services\AWS_Business_API\ApiCall;

class ApiCall {

    protected string $host = "na.business-api.amazon.com";
    protected string $accessKey = "";
    protected string $secretKey = "";
    protected string $accessToken = "";
    protected string $region = "us-east-1";
    protected string $service = "execute-api";
    protected string $requestUrl = "";
    protected string $uri = "";
    protected string $httpRequestMethod = "";
    protected string $queryString = "";
    protected string $terminationString = "aws4_request";
    protected string $algorithm = "AWS4-HMAC-SHA256";
    protected string $phpAlgorithm = "sha256";
    protected string $userEmail = "";
    protected string $signedHeaders = "host;x-amz-access-token;x-amz-date;x-amz-user-email";
    protected string $canonicalQueryString = "productRegion=US&locale=en_US";

    public function __construct() {

      $this->accessKey = config("app.aws_business_api_access_key");
      $this->secretKey = config("app.aws_business_api_secret_key");
      $this->userEmail = config("app.aws_business_api_email");

    }

    public function getRequest($accessToken, $queryString, $requestUrl, $uri) {

      $this->accessToken = $accessToken;
      $this->queryString = $queryString;
      $this->requestUrl = $requestUrl;
      $this->uri = $uri;
      $this->httpRequestMethod = "GET";

      $curl = curl_init();
      $url = $this->requestUrl."?".$this->canonicalQueryString;
      $headersFS = $this->headers();

      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->httpRequestMethod);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headersFS);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

      $server_APIoutput = curl_exec($curl);

      return $server_APIoutput;
      return json_decode($server_APIoutput);
    }

    public function headers() {

      $today = date("Ymd\THis\Z");
      $reqDate = date("Ymd");
      $Time = date("Ymd\THis\Z", strtotime('-7 hours', strtotime(date("Y-m-d H:i:s"))));

      $AwsSignature = $this->aws_signed_headers($today, $reqDate);

      return [
        'x-amz-access-token:' . $this->accessToken,
        'host:' . $this->host,
        $AwsSignature[0],
        'x-amz-date:' . $Time,
        'x-amz-user-email:'. $this->userEmail
      ];
    }

    public function aws_signed_headers ($today, $reqDate) {

        $kSigning = $this->kSigning($reqDate);
        $canonicalHeaders = $this->canonicalHeaders($today);
        $requestHasedPayload = $this->requestHasedPayload($this->queryString);
        $canonicalRequest = $this->canonicalRequest($this->uri, $canonicalHeaders, $requestHasedPayload);
        $credentialScope = $this->credentialScope($reqDate);
        $stringToSign = $this->stringToSign($today, $credentialScope, $canonicalRequest);
        $signature = $this->signature($stringToSign, $kSigning);
        $authorizationHeader = $this->authorizationHeader($credentialScope, $signature);

        return $this->requestHeaders($today, $authorizationHeader);
    }

    public function kSigning($reqDate) {

        $kDate = hash_hmac($this->phpAlgorithm, $reqDate, 'AWS4' . $this->secretKey, true);
        $kRegion = hash_hmac($this->phpAlgorithm, $this->region, $kDate, true);
        $kService = hash_hmac($this->phpAlgorithm, $this->service, $kRegion, true);

        return hash_hmac($this->phpAlgorithm, $this->terminationString, $kService, true);
    }

    public function canonicalHeaders($today) {

      $canonicalHeaders[] = 'host:' . $this->host;
      $canonicalHeaders[] = 'x-amz-access-token:' . $this->accessToken;
      $canonicalHeaders[] = 'x-amz-date:' . $today;
      $canonicalHeaders[] = 'x-amz-user-email:' . $this->userEmail;

      return implode("\n", $canonicalHeaders);
    }

    public function requestHasedPayload($data) {
       return hash($this->phpAlgorithm, $data);
    }

    public function canonicalRequest($canonicalURI, $canonicalHeadersStr, $requestHasedPayload) {

      $canonicalRequest[] = $this->httpRequestMethod;
      $canonicalRequest[] = $canonicalURI;
      $canonicalRequest[] = $this->canonicalQueryString;
      $canonicalRequest[] = $canonicalHeadersStr . "\n";
      $canonicalRequest[] = $this->signedHeaders;
      $canonicalRequest[] = $requestHasedPayload;
      $requestCanonicalRequest = implode("\n", $canonicalRequest);

      return  hash($this->phpAlgorithm, utf8_encode($requestCanonicalRequest));
    }

    public function credentialScope($reqDate) {

      $credentialScope[] = $reqDate;
      $credentialScope[] = $this->region;
      $credentialScope[] = $this->service;
      $credentialScope[] = $this->terminationString;

      return implode('/', $credentialScope);
    }

    public function stringToSign($today, $credentialScopeStr, $requestHasedCanonicalRequest) {

      $stringToSign[] = $this->algorithm;
      $stringToSign[] = $today;
      $stringToSign[] = $credentialScopeStr;
      $stringToSign[] = $requestHasedCanonicalRequest;

      return implode("\n", $stringToSign);
    }

    public function signature($stringToSignStr, $kSigning) {
      return hash_hmac($this->phpAlgorithm, $stringToSignStr, $kSigning);
    }

    public function authorizationHeader($credentialScopeStr, $signature) : string {

        $authorizationHeader[] = 'Credential=' . $this->accessKey . '/' . $credentialScopeStr;
        $authorizationHeader[] = 'SignedHeaders=' . $this->signedHeaders;
        $authorizationHeader[] = 'Signature=' . ($signature);

        return $this->algorithm . ' ' . implode(', ', $authorizationHeader);
    }

    public function requestHeaders($today, $authorizationHeaderStr) {

      $headers[] = 'Authorization:' . $authorizationHeaderStr;
      $headers[] = 'host: ' . $this->host;
      $headers[] = 'x-amz-date: ' . $today;
      $headers[] = 'x-amz-user-email:' . $this->userEmail;
      $headers[] = 'x-amz-access-token: ' . $this->accessToken;

      return $headers;
    }

}
