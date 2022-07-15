<?php

namespace App\Http\Controllers\shipntrack\Bombino;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\shipntrack\SMSA\SmsaExperessController;
use Illuminate\Support\Facades\Http;

class BombinoExpressController extends Controller
{
    public function index()
    {

        $bombino_account_id = 'PACI01';
        $bombino_user_id = 'pacifi147@gmail.com';
        $bombino_password = '123';
        $awb_no = 'US10001141';

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
        // echo $response;

        po((json_decode($response)));

        // return $arrayResult;
        // return $response;
    }
}
