<?php

function signRequest(){
        $method ='GET';
        $uri = '/dev';
        $json = file_get_contents('php://input');
        $obj = json_decode($json);


        if(isset($obj->method))
        {
            $m = explode("|", $obj->method);
            $method = $m[0];
            $uri .= $m[1];
        }


        $secretKey = $this->session->data['aws_secret'];
        $access_key = $this->session->data['aws_key'];
        $token = $this->session->data['aws_token'];
        $region = 'ap-southeast-1';
        $service = 'execute-api';

        $options = array(); $headers = array();
        $host = "YOUR-API-HOST.execute-api.ap-southeast-1.amazonaws.com";
//Or you can define your host here.. I am using API gateway.


        $alg = 'sha256';

        $date = new DateTime( 'UTC' );

        $dd = $date->format( 'Ymd\THis\Z' );

        $amzdate2 = new DateTime( 'UTC' );
        $amzdate2 = $amzdate2->format( 'Ymd' );
        $amzdate = $dd;

        $algorithm = 'AWS4-HMAC-SHA256';


        $parameters = (array) $obj->data;

           if($obj->data == null || empty($obj->data))
        {
            $obj->data = "";
        }else{
            $param = json_encode($obj->data);
            if($param == "{}")
            {
                $param = "";

            }

        $requestPayload = strtolower($param);
        $hashedPayload = hash($alg, $requestPayload);

        $canonical_uri = $uri;
        $canonical_querystring = '';

        $canonical_headers = "content-type:"."application/json"."\n"."host:".$host."\n"."x-amz-date:".$amzdate."\n"."x-amz-security-token:".$token."\n";
        $signed_headers = 'content-type;host;x-amz-date;x-amz-security-token';
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
                    'content-type'=>'application/json',
                    'x-amz-security-token'=> $token,
                    'x-amz-date'=>$amzdate,
                    'Authorization'=>$authorization_header];
        return $headers;

    }
