<?php

namespace App\Services\Zoho;

use ZipArchive;
use League\Csv\Reader;
use App\Models\MongoDB\zoho;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ZohoBulkReadAPI
{
    private $url = "https://www.zohoapis.com/crm/bulk/v2/read";
    private $token;

    public function zohoDump($zoho_id, $status, $page, $more_records)
    {
        $this->token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];

        Log::debug('zoho_id =>' . $zoho_id);
        Log::debug('status =>' . $status);
        Log::debug('page =>' . $page);
        Log::debug('more_records =>' . $more_records);
        if ($zoho_id) {

            if ($status == "COMPLETED") {

                $this->DownloadZohoFile($zoho_id);

                if ($more_records == 1) {

                    $this->MakeRequestIntoZoho($page + 1);
                }
            }
        }
    }

    public function MakeRequestIntoZoho($page = 1)
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
    }

    public function DownloadZohoFile($zoho_id)
    {
        Log::debug('download');
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
        Log::debug('csv_read');
        $csv_data =  Reader::createFromPath(Storage::path($csv_path), 'r');
        $csv_data->setDelimiter(',');
        $csv_data->setHeaderOffset(0);

        foreach ($csv_data as $data) {

            zoho::where('Alternate_Order_No', $data['Alternate_Order_No'])->where('ASIN', $data['ASIN'])->update($data, ['upsert' => true]);
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
