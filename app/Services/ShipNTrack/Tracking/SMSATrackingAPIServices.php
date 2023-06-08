<?php

namespace App\Services\ShipNTrack\Tracking;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Models\ShipNTrack\CourierTracking\SmsaTrackings;

class SMSATrackingAPIServices
{
    public function SMSA($records)
    {
        $awbNo = $records['awb_no'];
        $passKey = $records['pass_key'];
        $reference_id = $records['reference_id'];
        $process_management_id = $records['process_management_id'];

        $client = new Client();
        $headers = [
            'Content-Type' => 'text/xml'
        ];
        $body = '<?xml version=\'1.0\' encoding=\'utf-8\'?>
                <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <getTracking xmlns="http://track.smsaexpress.com/secom/">
                    <awbNo>' . $awbNo . '</awbNo>
                    <passkey>' . $passKey . '</passkey>
                    </getTracking>
                </soap:Body>
                </soap:Envelope>';
        $request = new Request('POST', 'http://track.smsaexpress.com/SeCom/SMSAwebService.asmx', $headers, $body);

        $response1 = $client->sendAsync($request)->wait();
        $plainXML = mungXML(trim($response1->getBody()));
        $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        // Log::debug($arrayResult);
        $this->SMSADataFormatting($arrayResult, $reference_id);

        $command_end_time = now();
        ProcessManagementUpdate($process_management_id, $command_end_time);
    }

    public function SMSADataFormatting($smsa_data, $reference_id)
    {
        $smsa_data = isset($smsa_data['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram']['NewDataSet']['Tracking']) ? $smsa_data['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram']['NewDataSet']['Tracking'] : [];

        if (!empty($smsa_data)) {

            $smsa_records = [];
            if (isset($smsa_data[0])) {

                foreach ($smsa_data as $smsa_value) {
                    $smsa_records[] = [
                        'account_id' => $reference_id,
                        'awbno' => $smsa_value['awbNo'],
                        'date' => date('Y-m-d H:i:s', strtotime($smsa_value['Date'])),
                        'activity' => $smsa_value['Activity'],
                        'details' => $smsa_value['Details'],
                        'location' => $smsa_value['Location']
                    ];
                }
            } else {
                $smsa_records[] = [
                    'account_id' => $reference_id,
                    'awbno' =>  $smsa_data['awbNo'],
                    'date' => date('Y-m-d H:i:s', strtotime($smsa_data['Date'])),
                    'activity' =>  $smsa_data['Activity'],
                    'details' =>  $smsa_data['Details'],
                    'location' =>  $smsa_data['Location']
                ];
            }

            SmsaTracking::upsert($smsa_records, ['awbno_date_activity_unique'], [
                'account_id',
                'awbno',
                'date',
                'activity',
                'details',
                'location',
            ]);
        }
    }
}
