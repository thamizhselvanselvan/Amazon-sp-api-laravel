<?php

namespace App\Services\Zoho;

class insertZohoOrder
{
    public function getAccessToken()
    {
        $zohoURL = "https://accounts.zoho.in/oauth/v2/token";

        $client_id = config('app.zoho_client_id');
        $client_secret = config('app.zoho_secret');
        $refres_token = config('app.zoho_refresh_token');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $zohoURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER =>  false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refres_token,
                'grant_type' => 'refresh_token'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        return $response;
    }

    public function addOrderItemsToZoho()
    {

        //
    }

    public function zohoOrderFormating()
    {

        //
    }

    public function getOrderDetails()
    {


        //
    }
}
