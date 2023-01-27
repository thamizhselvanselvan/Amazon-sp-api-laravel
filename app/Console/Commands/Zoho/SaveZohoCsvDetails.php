<?php

namespace App\Console\Commands\Zoho;

use League\Csv\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class SaveZohoCsvDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:save-csv-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save Csv Details into Database';
    public $token;
    public $file_details = 'Zoho_data/file_details.txt';
    public $url = 'https://www.zohoapis.com/crm/bulk/v2/read';
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
        $this->token = json_decode(Storage::get('zoho/access_token.txt'), true)['access_token'];

        $file = $this->checkId();

        $page = 1;
        if ($file) {
            $id = $file['id'];
            $response = $this->getFileStatus($id);
            po($response);
            $state = $response['data'][0]['state'];
            if ($state == 'COMPLETED') {

                $more_records = $response['data'][0]['result']['more_records'];
                Storage::put($this->file_details, json_encode($response));

                if ($more_records) {
                    $page = $response['data'][0]['result']['page'];
                    po($this->makeFileRequest($page + 1));
                }
                return $this->downloadFileAndUpdate($id);
            } else {
                Storage::put($this->file_details, json_encode($response));
                po($response);
            }
        }
    }

    public function makeFileRequest($page = 1)
    {
        $payload = [
            "callback" => [
                "url" => "https://catalog-manager-mosh.com/api/test/zoho/webhook",
                "method" => "post"
            ],
            'query' => [
                'module' => 'Leads',
                'page' => $page
            ]
        ];
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->url, $payload);

        $response = $response->json();
        $response = $this->verifyResponse($response);

        Storage::put($this->file_details, json_encode($response));
        return $response;
    }

    public function checkId()
    {
        $file_path = 'Zoho_data/file_details.txt';
        if (!Storage::exists($file_path)) {
            po($this->makeFileRequest(1));
            return false;
        }
        $files = json_decode(Storage::get($file_path), true);

        $code = isset($files['data'][0]['code'])
            ? $files['data'][0]['code'] : $files['data'][0]['state'];
        $id = isset($files['data'][0]['details']['id'])
            ? $files['data'][0]['details']['id'] : $files['data'][0]['id'];

        return [
            'code' => $code,
            'id' => $id
        ];
    }

    public function getFileStatus($id)
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->token,
        ])->get($this->url . '/' . $id);

        return $this->verifyResponse($response->json());
    }

    public function downloadFileAndUpdate($id)
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->token,
        ])->get($this->url . '/' . $id . '/result');

        $response = $this->verifyResponse($response);

        Storage::put("Zoho_data/$id.zip", $response);

        if ($this->extractZipFile("Zoho_data/$id.zip")) {
            return $this->saveCsvData("Zoho_data/$id.csv");
        }
    }

    public function saveCsvData($path)
    {
        $csv_data = CSV_Reader($path);
        $count = 0;
        $data_array = [];
        foreach ($csv_data as $data) {
            $count++;
            $data_array[] = $data;

            if ($count == 10000) {
                DB::connection('mongodb')
                    ->collection('zoho_datas')
                    ->insert($data_array);
                $count = 0;
                $data_array = [];
            }
        }

        if ($data_array) {
            DB::connection('mongodb')
                ->collection('zoho_datas')
                ->insert($data_array);
        }
        return 'Zoho Csv\'s data Saved Into Database';
    }

    public function extractZipFile($path)
    {
        $zip = new ZipArchive;
        $res = $zip->open(Storage::path($path));

        if ($res === TRUE) {
            $zip->extractTo(Storage::path('Zoho_data'));
            $zip->close();
            return true;
        }
        echo "File Not Found";
        return false;
    }

    public function verifyResponse($response)
    {
        if (isset($response['code']) &&  $response['code'] == 'INVALID_TOKEN') {
            po($response);
            exit;
        }
        return $response;
    }
}
