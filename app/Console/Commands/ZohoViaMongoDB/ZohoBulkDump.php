<?php

namespace App\Console\Commands\ZohoViaMongoDB;

use ZipArchive;
use Carbon\Carbon;
use League\Csv\Reader;
use App\Models\MongoDB\zoho;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ZohoBulkDump extends Command
{
    private $file_path = "ZohoResponse/zoho-response.txt";
    private $url = "https://www.zohoapis.com/crm/bulk/v2/read";
    public $token;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho-bulk-dump {zoho_id} {zoho_state} {page} {more_records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zoho dump bulk data into mongodb';

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
        $this->token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];
        // $zoho_id = $this->ReadZohoTextFile();
        $zoho_id = $this->argument('zoho_id');
        $status = $this->argument('zoho_state');
        $page = $this->argument('page');
        $more_records = $this->argument('more_records');
        if ($zoho_id) {
            // $response = $this->MonitorZohoRequest($zoho_id);
            // Log::alert($response);
            // $status = $response['data'][0]['state'];

            if ($status == "COMPLETED") {
                $this->DownloadZohoFile($zoho_id);
                // $more_records = $response['data'][0]['result']['more_records'];

                if ($more_records == 1) {
                    // $page = $response['data'][0]['result']['page'];
                    $this->MakeRequestIntoZoho($page + 1);
                }
            }
        }
    }

    // public function ReadZohoTextFile()
    // {
    //     echo 'read';
    //     if (!Storage::exists($this->file_path)) {
    //         $this->MakeRequestIntoZoho(1);
    //         // return false;
    //     }
    //     $file_details = json_decode(Storage::get($this->file_path), true);

    //     $zoho_id = isset($file_details['data'][0]['details']['id']) ? $file_details['data'][0]['details']['id'] : $file_details['data'][0]['id'];
    //     // $zoho_id = '1929333000107209152';
    //     return $zoho_id;
    // }

    public function MakeRequestIntoZoho($page = 1)
    {
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

    // public function MonitorZohoRequest($zoho_id)
    // {
    //     echo 'monitore';
    //     echo $zoho_id;
    //     $requestResponse = Http::withoutVerifying()->withHeaders([
    //         "Authorization" => "Zoho-oauthtoken " . $this->token
    //     ])->get($this->url . "/" . $zoho_id);

    //     $response = $requestResponse->json();
    //     Storage::put($this->file_path, json_encode($response));
    //     return $response;
    // }

    public function DownloadZohoFile($zoho_id)
    {
        echo 'download';
        $Response = Http::withoutVerifying()->withHeaders([
            "Authorization" => "Zoho-oauthtoken " . $this->token
        ])->get($this->url . "/" . $zoho_id . "/result");

        Storage::put("zohocsv/$zoho_id.zip", $Response);
        if ($this->ExtractZipFile("zohocsv/$zoho_id.zip")) {
            $this->csvReader("zohocsv/$zoho_id.csv");
        }
    }

    public function csvReader($csv_path)
    {
        echo 'csv read';
        $csv_data =  Reader::createFromPath(Storage::path($csv_path), 'r');
        $csv_data->setDelimiter(',');
        $csv_data->setHeaderOffset(0);
        foreach ($csv_data as $data) {
            $timestamp = [
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ];
            $records = [...$data, ...$timestamp];
            zoho::insert($records);
        }
    }

    public function ExtractZipFile($path)
    {
        echo 'extract file';
        $zip = new ZipArchive;
        if ($zip->open(Storage::path($path)) === TRUE) {
            $zip->extractTo(Storage::path('zohocsv'));
            $zip->close();
            return true;
        } else {
            echo 'File not found';
        }
    }
}
