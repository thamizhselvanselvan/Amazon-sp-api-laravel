<?php

namespace App\Services\ShipNTrack\Tracking;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Models\ShipNTrack\CourierTracking\SPLTracking;

class SPLTrackingAPIServices
{
    public function SPL($records)
    {
        
        $airwaybill = $records['awb_no'];
        $reference = '';
        $BranchCode = $records['account_id'];
        $CustomerCode = $records['key1'];
        $time_zone = $records['time_zone'];
        $process_management_id = $records['process_management_id'];

        $URL =  'https://dev.gnteq.app/api/gnconnect/Tracking' ; 

        // code to get the Authntication token of SPL API
        $filePath = storage_path('app/business/SPLToken.txt');
        $AuthToken = File::get($filePath);

        $payload =   [
                        "airwaybill"    =>  $airwaybill,
                        "reference"     =>  $reference,
                        "BranchCode"    =>  $BranchCode,
                        "CustomerCode"  =>  $CustomerCode 
                    ];

        Log::alert($payload);

        $response = Http::withToken($AuthToken)
                            ->withHeaders([
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                            ])
                            ->post($URL, $payload);


        $this->SPLDataFormatting($response, $reference, $time_zone);
        
        ProcessManagementUpdate($records['process_management_id'], Carbon::now());

    }

    protected function SPLDataFormatting($response, $reference, $time_zone)
    {
        // $SPL_data = isset(json_decode($response, true)['TrackingResults'][0]['Value']) ? json_decode($response, true)['TrackingResults'][0]['Value'] : [];

        if ($response->successful()) 
        {
            $SPL_data =  $response->json() ;         
            foreach ($SPL_data as $key => $value)
            {             
    
                $dataToUpsert =  [
                    'airwaybill' =>  $value['airwaybill'],
                    'eventCode' =>  $value['eventCode'],
                    'event' =>  $value['event'],
                    'eventName' =>  $value['eventName'],
                    'supplier' =>  $value['supplier'],
                    'userName' =>  $value['userName'],
                    'notes' =>  $value['notes'],
                    'actionDate' =>  Carbon::parse($value['actionDate'])->timezone($time_zone)->format('Y-m-d H:i:s'),
                    'eventCountry' =>  $value['eventCountry'],
                    'eventCity' =>  $value['eventCity'],
                    'eventSubCode' =>  $value['eventSubCode'],
                    'eventSubName' =>  $value['eventSubName'],
                ];            
    
                   SPLTracking::upsert($dataToUpsert, ['awbno_update_timestamp_description_unique'], [
                    'airwaybill',
                    'eventCode',
                    'event',
                    'eventName',
                    'supplier',
                    'userName',
                    'notes',
                    'actionDate',
                    'eventCountry',
                    'eventCity',
                    'eventSubCode',
                    'eventSubName'
                ]);
            }
        }
        else if ($response->status() === 401)
        {
            Log::notice("SPL Tracking  API Returned  Unauthorized Response ! , Generate token and try again.  ");
            return 0 ;
        }
        else
        {
            Log::error($response);
            Log::notice("An error occurred. Please try again later.");
            return 0 ;
        }


    }
}