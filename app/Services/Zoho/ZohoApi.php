<?php

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ZohoApi
{
    public $auth_token = null;
    public $zoho_lead_base_url = "";
    public $zoho_token_base_url = "";
    public $zoho_token_path = "zoho/access_token.txt";

    public function __construct()
    {
        $this->auth_token = $this->getAccessToken();

        if (app()->environment() === 'production') {
            $this->zoho_lead_base_url = "https://www.zohoapis.com/crm/v2/Leads";
            $this->zoho_token_base_url = "https://accounts.zoho.com/oauth/v2/token";
        } else {
            $this->zoho_lead_base_url = "https://www.zohoapis.in/crm/v2/Leads";
            $this->zoho_token_base_url = "https://accounts.zoho.in/oauth/v2/token";
        }
    }

    public function getAccessToken()
    {
        if (!Storage::exists($this->zoho_token_path)) {
            return false;
        }

        $response = json_decode(Storage::get($this->zoho_token_path), true);

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

    public function search($amazon_order, $item_order, $new_zoho  = null)
    {
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

    public function updateLead($lead_id, $parameters)
    {
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

    public function storeLead($parameters)
    {
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
}
