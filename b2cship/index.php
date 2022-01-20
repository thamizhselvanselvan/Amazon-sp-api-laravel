<?php
$url = "https://uat-api.b2cship.us/PacificAmazonAPI.svc/TrackingAmazon";

$xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
<AmazonTrackingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
<Validation>
<UserID>Amazon</UserID>
<Password>AcZmraDzLoxA4NxLUcyrWnSiEaXxRQkfJ9B5hCbiK5M=</Password>
</Validation>
<APIVersion>1.0</APIVersion>
<TrackingNumber>US10000053</TrackingNumber>
</AmazonTrackingRequest>';

//setting the curl headers
$headers = array(
    "Content-type: text/plain ;charset=\"utf-8\"",
    "Accept: application/plain",
);

try{

    $ch = curl_init();

    //setting the curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $xmlRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $data = curl_exec($ch);

    //convert the XML result into array
    if($data === false){
        $error = curl_error($ch);
        echo $error;
        die('error occured');
    }else{
        $data = json_decode(json_encode(simplexml_load_string($data)), true);
        return $data;
    }

    curl_close($ch);

}catch(Exception  $e){
    echo 'Message: '.$e->getMessage();
    die("Error");
}