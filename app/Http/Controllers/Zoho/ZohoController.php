<?php

namespace App\Http\Controllers\Zoho;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ZohoController extends Controller
{
    private function getAccessToken()
    {
        $zohoURL = "https://accounts.zoho.in/oauth/v2/token";
        $clientID = '1000.FY2B09NCY9PFBOT4FTRM0GEMXKCO2I';
        $clientSecret = 'd050ac81701d158c1903037082674034ace0d9538f';
        $refres_token = '1000.5446197eabbe5bf255aa17617cd27aad.a1fd478a681bd1581c541b8a1b78d380';

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
                'client_id' => $clientID,
                'client_secret' => $clientSecret,
                'refresh_token' => $refres_token,
                'grant_type' => 'refresh_token'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        echo "The Refresh Code";
        po($response);
        echo "<br>";
        $response = json_decode($response, true);
        // dd($response);
        return $response;
    }

    public function getOrderDetails(Request $request, $leadId)
    {
        $leadId = trim($leadId, 'zcrm_');
        $leadId  = '389763000000274001';
        $accessToken = $this->getAccessToken();
        $token = $accessToken['access_token'];
        $headers = [
            'Authorization' => 'Zoho-oauthtoken ' . $token,
        ];
        $zohoURL = 'https://www.zohoapis.in/crm/v2/Leads/';

        $CompleteURI = $zohoURL . $leadId;
        $response = Http::withHeaders($headers)->get($CompleteURI);
        $response = json_decode($response);
        debug($response);
        dd($response->data->data);
        exit;


        $response = ($response->data[0]);

        return response()->json($response);
    }

    public function insertZohoOrder($data, $accessToken)
    {
    }


    public function addOrderItemsToZoho()
    {
        $url = "https://www.zohoapis.in/crm/v2/Leads";
        $token = $this->getAccessToken();
        $authtoken = '';
        if ($token) {
            $authtoken = $token['access_token'];
        }

        $requestBody = array();
        $recordArray = array();
        $recordObject = array();

        $recordObject["Company"] = "FieldAPIValue";
        $recordObject["Last_Name"] = "347706107420006";
        $recordObject["First_Name"] = "34770617420006";
        $recordObject["State"] = "FieldAPIValue";
        $recordArray[] = $recordObject;
        $requestBody["data"] = $recordArray;

        $curl_pointer = curl_init();
        $curl_options = array();
        $headersArray = array();
        $headersArray[] = "Authorization" . ":" . "Zoho-oauthtoken $authtoken";

        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_RETURNTRANSFER] = true;
        $curl_options[CURLOPT_HEADER] = 1;
        $curl_options[CURLOPT_CUSTOMREQUEST] = "POST";
        $curl_options[CURLOPT_POSTFIELDS] = json_encode($requestBody);
        $curl_options[CURLOPT_HTTPHEADER] = $headersArray;

        curl_setopt_array($curl_pointer, $curl_options);
        $result = curl_exec($curl_pointer);

        $content = preg_split('/[\r\n]/', $result, -1, PREG_SPLIT_NO_EMPTY);
        $array = (($content[count($content) - 1]));

        $result = (json_decode($array));
        $response['id'] = ($result->data[0]->details->id);
        $response['status'] = ($result->data[0]->status);

        po($result);
        dd($response);
        exit;

        return true;
        $orderItems = DB::connection('order')->select("
            SELECT *, oid.shipping_address 
            FROM 
                orders AS os
            INNER JOIN orderitemdetails AS oid
            ON
                os.amazon_order_identifier = oid.amazon_order_identifier
            INNER JOIN ord_order_seller_credentials AS oosc
            ON
                oosc.seller_id = os.our_seller_identifier
            LIMIT 1
        ");

        if (count($orderItems) > 0) {
            $count = 0;
            foreach ($orderItems as $val1) {

                $accessToken = $this->getAccessToken();

                $val1 = json_decode(json_encode($val1), true);
                $OrderItemtoZoho = $this->insertOrderItemtoZoho($val1);

                $responseZoho = $this->insertZohoOrder($OrderItemtoZoho, $accessToken);
                po($responseZoho);
                exit;
            }
        }
    }

    public function insertOrderItemtoZoho($token)
    {
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
        $recordObject["Company"] = "FieldAPIValue";
        $recordObject["Last_Name"] = "347706107420006";
        $recordObject["First_Name"] = "34770617420006";
        $recordObject["State"] = "FieldAPIValue";

        $recordArray[] = $recordObject;
        $requestBody["data"] = $recordArray;
        $curl_options[CURLOPT_POSTFIELDS] = json_encode($requestBody);
        $headersArray = array();

        $headersArray[] = "Authorization" . ":" . "Zoho-oauthtoken $token";

        $curl_options[CURLOPT_HTTPHEADER] = $headersArray;

        curl_setopt_array($curl_pointer, $curl_options);

        $result = curl_exec($curl_pointer);
        $responseInfo = curl_getinfo($curl_pointer);
        curl_close($curl_pointer);


        po($result);
    }
    public function getrecord()
    {
        $leadId  = '389763000000243001';

        // $leadId = trim($leadId, 'zcrm_');

        $accessToken = $this->getAccessToken();
        $token = $accessToken['access_token'];
        $headers = [
            'Authorization' => 'Zoho-oauthtoken ' . $token,
        ];
        $zohoURL = 'https://www.zohoapis.com/crm/v2/Leads/';

        $CompleteURI = $zohoURL . $leadId;
        $response = Http::withHeaders($headers)->get($CompleteURI);
        $response = json_decode($response);
        $response = ($response->data[0]);
    }

    public function isStringSet($string)
    {
        return (!is_null($string) && $string !== '');
    }
}
