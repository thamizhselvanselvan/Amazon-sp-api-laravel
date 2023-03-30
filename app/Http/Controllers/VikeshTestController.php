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
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

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
        $OrderByColunm = [
            'SMSA' => 'date',
            'Aramex' => 'update_date_time',
            'Bombino' => 'action_date'

        ];
        $selectColumns = [
            'SMSA' => [
                'date',
                'activity',
                'location',
            ],
            'Aramex' => [
                'update_date_time',
                'update_description',
                'update_location',
            ],
            'Bombino' => [
                'awbno',
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

        ];

        $destination = substr($request->awbNo, 0, 2);
        $awbNo = substr($request->awbNo, 2);

        $data = [];
        if ($destination == 'AE') {

            $data = Trackingae::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        } elseif ($destination == 'SA') {

            $data = Trackingksa::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        }

        $forwarder1_data = [];
        $forwarder2_data = [];

        foreach ($data as $key => $records) {

            if (($records['forwarder_1_awb'] != '')) {

                $awb_no = $records['forwarder_1_awb'];
                $courier_name = $records['courier_partner1']['courier_names']['courier_name'];

                $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

                $forwarder1_data = $table->select($selectColumns[$courier_name])
                    ->where('awbno', $awb_no)
                    ->orderBy($OrderByColunm[$courier_name], 'DESC')
                    ->get()
                    ->toArray();

                if ($courier_name == 'SMSA') {

                    foreach ($forwarder1_data as $key => $records1) {
                        $forwarder1_data[$key] = [
                            'event_detail' => $records1['activity'],
                            'action_date' => date('Y-m-d', strtotime($records1['date'])),
                            'action_time' => date('H:i:s', strtotime($records1['date'])),
                            'location' => $records1['location']
                        ];
                    }
                } else if ($courier_name == 'Aramex') {

                    foreach ($forwarder1_data as $key => $records2) {
                        $forwarder1_data[$key] = [
                            'event_detail' => $records2['update_description'],
                            'action_date' => date('Y-m-d', strtotime($records2['update_date_time'])),
                            'action_time' => date('H:i:s', strtotime($records2['update_date_time'])),
                            'location' => $records2['update_location']
                        ];
                    }
                }
            }
            if (($records['forwarder_2_awb'] != '')) {

                $awb_no = $records['forwarder_2_awb'];
                $courier_name = $records['courier_partner2']['courier_names']['courier_name'];
                $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

                $forwarder2_data = $table->select($selectColumns[$courier_name])
                    ->where('awbno', $awb_no)
                    ->orderBy($OrderByColunm[$courier_name], 'DESC')
                    ->get()
                    ->toArray();
                if ($courier_name == 'SMSA') {

                    foreach ($forwarder2_data as $key => $records2) {
                        $forwarder2_data[$key] = [
                            'event_detail' => $records2['activity'],
                            'action_date' => date('Y-m-d', strtotime($records2['date'])),
                            'action_time' => date('H:i:s', strtotime($records2['date'])),
                            'location' => $records2['location']
                        ];
                    }
                } else if ($courier_name == 'Aramex') {

                    foreach ($forwarder2_data as $key => $records2) {
                        $forwarder2_data[$key] = [
                            'event_detail' => $records2['update_description'],
                            'action_date' => date('Y-m-d', strtotime($records2['update_date_time'])),
                            'action_time' => date('H:i:s', strtotime($records2['update_date_time'])),
                            'location' => $records2['update_location']
                        ];
                    }
                }
            }
        }
        $result = [...$forwarder1_data, ...$forwarder2_data];

        return $result;
    }
}
