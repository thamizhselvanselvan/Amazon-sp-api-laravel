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
            echo $id;
            $response = $this->getFileStatus($id);

            $state = $response['data'][0]['state'];
            if ($state == 'COMPLETED') {

                $more_records = $response['data'][0]['result']['more_records'];
                if ($more_records) {
                    $page = $response['data'][0]['result']['page'];
                    po($this->makeFileRequest($page + 1));
                }
                $this->downloadFileAndUpdate($id);
            } else {

                $file_path = 'zoho_data/file_details.txt';
                Storage::put($file_path, json_encode($response));

                po($response);
            }
        }
    }

    public function makeFileRequest($page = 1)
    {
        $url = 'https://www.zohoapis.com/crm/bulk/v2/read';

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
            ])->post($url, $payload);

        $response = $response->json();

        $file_path = 'zoho_data/file_details.txt';

        Storage::put($file_path, json_encode($response));
        return $response;
    }

    public function checkId()
    {
        $file_path = 'zoho_data/file_details.txt';
        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');

            po($this->makeFileRequest(1));
            return false;
        }
        $files = json_decode(Storage::get($file_path), true);

        $code = $files['data'][0]['code'];
        $id = $files['data'][0]['details']['id'];

        return [
            'code' => $code,
            'id' => $id
        ];
    }

    public function getFileStatus($id)
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->token,
        ])->get('https://www.zohoapis.com/crm/bulk/v2/read/' . $id);

        return $response->json();
    }

    public function downloadFileAndUpdate($id)
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->token,
        ])->get('https://www.zohoapis.com/crm/bulk/v2/read/' . $id . '/result');

        Storage::put("zohoData/$id.zip", $response);

        $zip = new ZipArchive;
        $res = $zip->open(Storage::path("zohoData/$id.zip"));

        if ($res === TRUE) {

            $zip->extractTo(
                '/Destination/Directory/',
                array('H_W.gif', 'helloworld.php')
            );

            $zip->close();
            echo 'Unzipped Process Successful!';
        } else {
            echo 'Unzipped Process failed';
        }

        $csv_data = CSV_Reader('zohoData/$id.zip');

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
    }
}
