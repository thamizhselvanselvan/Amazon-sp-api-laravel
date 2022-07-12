<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::get("samsa/test", function () {


    $url = "http://track.smsaexpress.com/secom/getTracking";

    $xmlRequest = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <getTracking xmlns="http://track.smsaexpress.com/secom/">
          <awbNo>29031433354</awbNo>
          <passkey>Bom@7379</passkey>
        </getTracking>
      </soap:Body>
    </soap:Envelope>';

    //setting the curl headers
    $headers = array(
        "Content-type: text/plain ;charset=\"utf-8\"",
        "Accept: application/plain",
    );

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
    po($data);
});
