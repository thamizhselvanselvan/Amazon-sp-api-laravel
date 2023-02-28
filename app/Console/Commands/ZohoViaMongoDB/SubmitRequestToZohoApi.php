<?php

namespace App\Console\Commands\ZohoViaMongoDB;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SubmitRequestToZohoApi extends Command
{
    private $file_path = "ZohoResponse/zoho-response.txt";
    private $url = "https://www.zohoapis.com/crm/bulk/v2/read";
    public $token;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:submit-request-to-zoho';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send requset to zoho api for downloading the zoho data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $page = 1;
        $this->token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];

        $payload = [
            "callback" => [
                "url" => "https://catalog-manager-mosh.com/api/zoho/webhook",
                "method" => "post"
            ],
            "query" => [
                "module" => "Leads",
                "page" => $page
            ]
        ];

        $headers = Http::withoutVerifying()->withHeaders([
            "Authorization" => "Zoho-oauthtoken " . $this->token,
            "Content-Type" => "application/json"
        ])->post($this->url, $payload);

        // $response = $headers->json();
        // Storage::put($this->file_path, json_encode($response));
        // return $response;
    }
}
