<?php

namespace App\Http\Controllers;

use ZipArchive;
use Carbon\Carbon;
use App\Models\TestMongo;
use App\Models\MongoDB\zoho;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;

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

    public function bombinoAPI(Request $request)
    {
        $selectColumns = [
            'smsa' => [
                'awbno',
                'date',
                'activity',
                'details',
                'location',
            ],
            'am_ae' => [
                'awbno',
                'update_code',
                'update_description',
                'update_date_time',
                'update_location',
                'comment',
                'gross_weight',
                'chargeable_weight',
                'weight_unit',
            ],
            'Bombino' => [
                'awb_no',
                'consignee',
                'consignor',
                'destination',
                'status',
                'origin',
                'event_detail',
                'action_date',
                'action_time',
                'event_code',
                'location',
            ],
            'ss_ksa' => [
                'awbno',
                'date',
                'activity',
                'details',
                'location',
            ],
            'am_ksa' => [
                'awbno',
                'update_code',
                'update_description',
                'update_date_time',
                'update_location',
                'comment',
                'gross_weight',
                'chargeable_weight',
                'weight_unit',
            ],
        ];
        $modelPath = ['smsa' => 'SMSA', 'Bombino' => 'Bombino'];
        $modelName = ['smsa' => 'SmsaTrackings', 'Bombino' => 'BombinoTracking'];
        $tableName = ['smsa' => 'smsa_trackings', 'Bombino' => 'bombino_trackings'];


        $data = Trackingae::with(['CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4'])
            ->where('awb_number', $request->awbNo)
            ->get()
            ->toArray();

        $records1 = [];
        $records2 = [];
        foreach ($data as $key => $records) {
            if ($records['forwarder_1_flag'] == 0 && $records['forwarder_1_awb'] != '') {

                $forwarder1_awb = $records['forwarder_1_awb'];
                $forwarder_name = $records['courier_partner1']['name'];
                $table = table_model_change(model_path: $modelPath[$forwarder_name], model_name: $modelName[$forwarder_name], table_name: $tableName[$forwarder_name]);
                $records1 = $table->select($selectColumns[$forwarder_name])
                    ->where('awb_no', $forwarder1_awb)
                    ->orderBy('action_date', 'DESC')
                    ->get()
                    ->toArray();
            }
            if ($records['forwarder_2_flag'] == 0 && $records['forwarder_2_awb'] != '') {

                $forwarder2_awb = $records['forwarder_2_awb'];
                $forwarder_name = $records['courier_partner2']['name'];
                $table = table_model_change(model_path: $modelPath[$forwarder_name], model_name: $modelName[$forwarder_name], table_name: $tableName[$forwarder_name]);
                $records2 = $table->select($selectColumns[$forwarder_name])
                    ->where('awbno', $forwarder2_awb)
                    ->orderBy('date', 'DESC')
                    ->get()
                    ->toArray();

                foreach ($records2 as $key => $records) {
                    $records2[$key] = [
                        'event_detail' => $records['activity'],
                        'action_date' => date('Y-m-d', strtotime($records['date'])),
                        'action_time' => date('H:i:s', strtotime($records['date'])),
                        'location' => $records['location']
                    ];
                }
            }
        }
        $result = [...$records1, ...$records2];

        return $result;
    }
}
