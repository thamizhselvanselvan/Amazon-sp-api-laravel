<?php

namespace App\Console\Commands\ZohoViaMongoDB;

use App\Models\MongoDB\zoho;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
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
        // Process Management begin

        $process_manage = [
            'module'             => 'Zoho Dump',
            'description'        => 'Dump data into App360 database from zoho database',
            'command_name'       => 'mosh:submit-request-to-zoho',
            'command_start_time' => now(),
        ];
        ProcessManagement::create($process_manage)->toArray();

        // Process Management end

        $records = zoho::count();
        $page = $records == 0 ? 1 : 4;

        $this->token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];

        $payload = [
            "callback" => [
                "url" => "https://app.360ecom.io/api/zoho/webhook",
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

        $response = $headers->json();
        Log::debug($response);
        // Storage::put($this->file_path, json_encode($response));
        // return $response;
    }
}
