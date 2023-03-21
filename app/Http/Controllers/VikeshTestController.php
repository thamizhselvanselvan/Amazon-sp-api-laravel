<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\MongoDB\zoho;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TestMongo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VikeshTestController extends Controller
{
    private $file_path = "ZohoResponse/zoho-response3.txt";
    private $url = "https://www.zohoapis.com/crm/bulk/v2/read";
    public $token;
    public function index()
    {
        $this->token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];
        $zoho_id = $this->ReadZohoTextFile();
        if ($zoho_id) {
            $response = $this->MonitorZohoRequest($zoho_id);
            po($response);
            $status = $response['data'][0]['state'];
            if ($status == "COMPLETED") {
                $this->DownloadZohoFile($zoho_id);
                $more_records = $response['data'][0]['result']['more_records'];
                echo $more_records;
                if ($more_records == 1) {
                    $page = $response['data'][0]['result']['page'];
                    $this->MakeRequestIntoZoho($page + 1);
                }
            }
        }
    }

    public function ReadZohoTextFile()
    {
        echo 'read';
        if (!Storage::exists($this->file_path)) {
            $this->MakeRequestIntoZoho(1);
            // return false;
        }
        $file_details = json_decode(Storage::get($this->file_path), true);

        $zoho_id = isset($file_details['data'][0]['details']['id']) ? $file_details['data'][0]['details']['id'] : $file_details['data'][0]['id'];
        // $zoho_id = '1929333000107209152';
        return $zoho_id;
    }

    public function MakeRequestIntoZoho($page = 1)
    {
        $payload = [
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
        Storage::put($this->file_path, json_encode($response));
        return $response;
    }

    public function MonitorZohoRequest($zoho_id)
    {
        echo 'monitore';
        echo $zoho_id;
        $requestResponse = Http::withoutVerifying()->withHeaders([
            "Authorization" => "Zoho-oauthtoken " . $this->token
        ])->get($this->url . "/" . $zoho_id);

        $response = $requestResponse->json();
        Storage::put($this->file_path, json_encode($response));
        return $response;
    }

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
        $csv_data = CSV_Reader($csv_path);
        $count = 0;
        $records = [];
        $asin = [];
        $order_no = [];
        foreach ($csv_data as $data) {
            $records[] = $data;
            $asin[] = $data['ASIN'];
            $order_no = $data['Alternate_Order_No'];
            // po($data);
            zoho::where('ASIN', $data['ASIN'])->where('Alternate_Order_No', $data['Alternate_Order_No'])->update($data, ['upsert' => true]);
            if ($count == 100) {

                // TestMongo::whereIn('ASIN', $asin)->whereIn('Alternate_Order_No', $order_no)->update($records, ['upsert' => true]);
                $count = 0;
                $records = [];
            }
            $count++;
        }
        TestMongo::whereIn('ASIN', $asin)->where('Alternate_Order_No', $order_no)->update($records, ['upsert' => true]);
    }

    public function ExtractZipFile($path)
    {
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
