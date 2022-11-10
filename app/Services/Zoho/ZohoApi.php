<?php

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Http;

class ZohoApi
{

    public function getAccessToken()
    {

        $client_id = config('app.zoho_client_id');
        $client_secret = config('app.zoho_secret');
        $refres_token = config('app.zoho_refresh_token');

        // $request = Http::asForm()->post("https://accounts.zoho.in/oauth/v2/token", [
        //     'client_id' => $client_id,
        //     'client_secret' => $client_secret,
        //     'refresh_token' => $refres_token,
        //     'grant_type' => 'refresh_token'
        // ]);

        // if ($request->ok()) {
        //     return $request->json();
        // }

        // return false;

        // dd($client_id);
        $zohoURL = "https://accounts.zoho.in/oauth/v2/token";
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
        // dd($response);
        return $response['access_token'];
    }

    public function insert($prod_array)
    {
        $auth_token = $this->getAccessToken();

        // $request = Http::withHeaders([
        //     'Authorization' => "Zoho-oauthtoken $auth_token"
        // ])->post("https://www.zohoapis.in/crm/v2/Leads", ['data' => [$prod_array]]);

        // if ($request->ok()) {
        // }

        $curl_pointer = curl_init();

        $curl_options = array();
        $url = "https://www.zohoapis.in/crm/v2/Leads";

        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_RETURNTRANSFER] = true;
        $curl_options[CURLOPT_HEADER] = 1;
        $curl_options[CURLOPT_CUSTOMREQUEST] = "POST";
        $requestBody = array();
        $recordArray = array();
        $recordObject = array();

        $recordArray[] = $prod_array;
        $requestBody["data"] = $recordArray;
        $curl_options[CURLOPT_POSTFIELDS] = json_encode($requestBody);
        $headersArray = array();

        $headersArray[] = "Authorization" . ":" . "Zoho-oauthtoken $auth_token";

        $curl_options[CURLOPT_HTTPHEADER] = $headersArray;

        curl_setopt_array($curl_pointer, $curl_options);

        $result = curl_exec($curl_pointer);

        return $result;
    }
}
