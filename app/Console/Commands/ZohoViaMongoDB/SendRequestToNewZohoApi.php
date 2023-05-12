<?php

namespace App\Console\Commands\ZohoViaMongoDB;

use App\Models\MongoDB\NewZoho;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SendRequestToNewZohoApi extends Command
{
    private $url = "https://www.zohoapis.com/crm/bulk/v2/read";
    private $token;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:send-request-to-new-zoho';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            'description'        => 'Dump data into MongoDB database from new zoho database',
            'command_name'       => 'mosh:send-request-to-new-zoho',
            'command_start_time' => now(),
        ];
        ProcessManagement::create($process_manage)->toArray();

        // Process Management end

        $counts = NewZoho::count();
        $page = ceil($counts / 200000);

        $this->token = json_decode(Storage::get("new_zoho/access_token.txt"), true)["access_token"];

        $payload = [
            "callback" => [
                "url" => "https://app.360ecom.io/api/zoho/new/webhook",
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
