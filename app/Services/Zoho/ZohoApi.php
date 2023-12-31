<?php

namespace App\Services\Zoho;

use Carbon\Carbon;
use App\Models\TestZoho;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ZohoApi
{
    public $auth_token = null;
    public $zoho_lead_base_url = "";
    public $zoho_token_base_url = "";
    public $zoho_token_path = "zoho/access_token.txt";
    public $new_zoho_token_path = "new_zoho/access_token.txt";

    public function __construct(bool $new_zoho)
    {

        $this->auth_token = $this->getAccessToken($new_zoho);

        if (app()->environment() === 'production') {
            $this->zoho_lead_base_url = "https://www.zohoapis.com/crm/v2/Leads";
            $this->zoho_token_base_url = "https://accounts.zoho.com/oauth/v2/token";
        } else {
            $this->zoho_lead_base_url = "https://www.zohoapis.in/crm/v2/Leads";
            $this->zoho_token_base_url = "https://accounts.zoho.in/oauth/v2/token";
        }
    }

    public function getAccessToken($new_zoho)
    {

        $path = ($new_zoho) ? $this->new_zoho_token_path : $this->zoho_token_path;

        if (!Storage::exists($path)) {
            return false;
        }

        $response = json_decode(Storage::get($path), true);

        return $response['access_token'] ?? null;
    }

    public function generateAccessToken()
    {

        $request = Http::asForm()->post($this->zoho_token_base_url, [
            'client_id' => config('app.zoho_client_id'),
            'client_secret' => config('app.zoho_secret'),
            'refresh_token' => config('app.zoho_refresh_token'),
            'grant_type' => 'refresh_token'

        ]);

        if ($request->status() == 200) {

            if (!Storage::exists($this->zoho_token_path)) {
                Storage::put($this->zoho_token_path, '');
            }

            Storage::put($this->zoho_token_path, json_encode($request->json()));

            print("Zoho Access Token Generated Successfully");
            return true;
        }

        $slackMessage = json_encode($request->json());
        Log::channel('slack')->info($slackMessage);
        return false;
    }

    public function generateNewAccessToken()
    {

        $request = Http::asForm()->post($this->zoho_token_base_url, [
            'client_id' => config('app.new_zoho_client_id'),
            'client_secret' => config('app.new_zoho_secret'),
            'refresh_token' => config('app.new_zoho_refresh_token'),
            'grant_type' => 'refresh_token'
        ]);

        if ($request->status() == 200) {

            if (!Storage::exists($this->new_zoho_token_path)) {
                Storage::put($this->new_zoho_token_path, '');
            }

            Storage::put($this->new_zoho_token_path, json_encode($request->json()));

            print("Zoho Access Token Generated Successfully");
            return true;
        }

        $slackMessage = json_encode($request->json());
        Log::channel('slack')->info($slackMessage);
        return false;
    }

    public function getLead($lead_id)
    {
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->auth_token
            ])->get($this->zoho_lead_base_url . '/' . $lead_id);

        if ($response->ok()) {
            return $response->json();
        }

        return false;
    }

    public function search($amazon_order, $item_order, $type,$new_zoho = null)
    {
        TestZoho::create(['opertaion_type' => 'search', 'api_called_through' => $type, 'time' => Carbon::now()]);
        $payment_key = ($new_zoho) ? 'Payment_Reference_Number' : 'Payment_Reference_Number1';
        $search_criteria = "?criteria=((Alternate_Order_No:equals:$amazon_order)and($payment_key:equals:$item_order))";

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->auth_token
            ])->get($this->zoho_lead_base_url . '/search' . $search_criteria);

        if ($response->ok()) {
            return $response->json();
        }

        return false;
    }

    public function updateLead($lead_id, $parameters, $type)
    {
        TestZoho::create(['opertaion_type' => 'udate_lead', 'api_called_through' => $type, 'time' => Carbon::now()]);

        $parameters["id"] = $lead_id;

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->auth_token
            ])->put($this->zoho_lead_base_url . '/' . $lead_id, [
                "data" => [$parameters]
            ]);

        if ($response->ok()) {
            return $response->json();
        }

        dd($response->body());

        return false;
    }

    public function storeLead($parameters, $type)
    {
        TestZoho::create(['opertaion_type' => 'store_lead', 'api_called_through' => $type, 'time' => Carbon::now()]);
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->auth_token
            ])->post($this->zoho_lead_base_url, [
                "data" => [$parameters]
            ]);

        if ($response->status() == 201) {
            return $response->json();
        }

        return $response->body();
    }

    public function deleteLead(array $parameters)
    {

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->auth_token
            ])->delete($this->zoho_lead_base_url . "?ids=" . implode(",", $parameters));

        if ($response->status() == 201) {
            return $response->json();
        }

        return $response->body();
    }
}
