<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\seller\AsinMasterSeller;
use App\Models\seller\SellerAsinDetails;
use Illuminate\Support\Facades\Response;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::get("samsa/test", function () {

   $awb_no = 'US10000141';
   $bombino_account_id = config('database.bombino_account_id');
   $bombino_user_id = config('database.bombino_user_id');
   $bombino_password = config('database.bombino_password');

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
   $response = json_decode($response);
   po($response);
});
