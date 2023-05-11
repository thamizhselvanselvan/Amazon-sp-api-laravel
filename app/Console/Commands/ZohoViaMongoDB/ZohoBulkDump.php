<?php

namespace App\Console\Commands\ZohoViaMongoDB;

use App\Models\MongoDB\NewZoho;
use ZipArchive;
use Carbon\Carbon;
use League\Csv\Reader;
use App\Models\MongoDB\zoho;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
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
    protected $signature = 'mosh:zoho-bulk-dump {zoho_id} {zoho_state} {page} {more_records} {token_file_name} {callback_url}';

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
        // $zoho_id = $this->ReadZohoTextFile();
        $zoho_id = $this->argument('zoho_id');
        $status = $this->argument('zoho_state');
        $page = $this->argument('page');
        $more_records = $this->argument('more_records');
        $token_file = $this->argument('token_file_name');
        $callback_url = $this->argument('callback_url');

        $this->token = json_decode(Storage::get("$token_file/access_token.txt"), true)["access_token"];

        Log::debug('zoho_id =>' . $zoho_id);
        Log::debug('status =>' . $status);
        Log::debug('page =>' . $page);
        Log::debug('more_records =>' . $more_records);
        if ($zoho_id) {

            if ($status == "COMPLETED") {

                $this->DownloadZohoFile($zoho_id, $token_file);

                if ($more_records == 1) {

                    $this->MakeRequestIntoZoho($page + 1, $callback_url);
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

    public function MakeRequestIntoZoho($page = 1, $callback_url)
    {
        $process_manage = [
            'module'             => 'Zoho Dump',
            'description'        => 'Dump data into App360 database from zoho database ' . $page,
            'command_name'       => 'mosh:submit-request-to-zoho',
            'command_start_time' => now(),
        ];
        ProcessManagement::create($process_manage)->toArray();

        $payload = [
            "callback" => [
                "url" => $callback_url,
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

    public function DownloadZohoFile($zoho_id, $token_file)
    {
        Log::debug('download');
        $Response = Http::withoutVerifying()->withHeaders([
            "Authorization" => "Zoho-oauthtoken " . $this->token
        ])->get($this->url . "/" . $zoho_id . "/result");

        Storage::put("zohocsv/$zoho_id.zip", $Response);
        if ($this->ExtractZipFile("zohocsv/$zoho_id.zip")) {
            $this->csvReader("zohocsv/$zoho_id.csv", $token_file);
        }
    }

    public function csvReader($csv_path, $token_file)
    {
        Log::debug('csv_read');
        $csv_data =  Reader::createFromPath(Storage::path($csv_path), 'r');
        $csv_data->setDelimiter(',');
        $csv_data->setHeaderOffset(0);

        Log::debug($token_file);

        if ($token_file == "zoho") {

            foreach ($csv_data as $data) {

                zoho::where('Alternate_Order_No', $data['Alternate_Order_No'])->where('ASIN', $data['ASIN'])->update($data, ['upsert' => true]);
            }
        } else {
            foreach ($csv_data as $data) {

                NewZoho::insert($data);
                // NewZoho::where('Alternate_Order_No', $data['Alternate_Order_No'])->where('ASIN', $data['ASIN'])->update($data, ['upsert' => true]);
            }
        }
        Log::debug(count($csv_data));

        $processManagementID = ProcessManagement::where('module', 'Zoho Dump')
            ->where('command_name', 'mosh:submit-request-to-zoho')
            ->where('command_end_time', '0000-00-00 00:00:00')
            ->get('id')
            ->first();

        $pm_id = $processManagementID['id'];
        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);

        Log::debug('inserted.');
    }

    public function ExtractZipFile($path)
    {
        Log::debug('extract file');
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
