<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToArray;
use Yajra\DataTables\Facades\DataTables;
use phpDocumentor\Reflection\Types\Null_;

class TrackingStatusController extends Controller
{
    public $micro_status = NULL;
    public function trackingStatusDetails(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->trackingStatusDetailsData();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return view('b2cship.trackingStatus.index');
    }

    public function trackingStatusDetailsData()
    {
        $PODeventsArray = [];
        $offset = 0;

        $PODtransEvents = DB::connection('mssql')->select("SELECT DISTINCT StatusDetails, FPCode FROM PODTrans ");
        // WHERE StatusDetails NOT LIKE '%\[BOMBINO]%' ESCAPE '\' 

        // Making By default null for every code and description
        foreach ($PODtransEvents as $PODtransEvent) {

            $fpCode = $PODtransEvent->FPCode;
            $statusDetails = $PODtransEvent->StatusDetails;
            if ($fpCode == '') {
                $fpCode = 'B2CShip';
            }
            $ignorebombion = '[BOMBINO]';
            $ignoreDelivered = 'BOMBINO : DELIVERED';
            $ignoreOFD = 'BOMBINO : Out for Delivery';
            $ignoreRunNO = 'BOMBINO : Run No. : 76764951 Status: FLIGHT DELAY Description: flight delay';
            $ignoreUnDelivered = 'BOMBINO : UN-DELIVERED';

            // $statusDetails = strtoupper($statusDetails);
            $trackingMsg = $fpCode . ' : ' . $statusDetails;
            if ((!str_contains($trackingMsg, $ignorebombion)) && (!str_contains($trackingMsg, $ignoreDelivered)) && (!str_contains($trackingMsg, $ignoreOFD)) && (!str_contains($trackingMsg, $ignoreRunNO)) && (!str_contains($trackingMsg, $ignoreUnDelivered))) {

                $PODeventsArray[$offset]['TrackingMsg'] = $trackingMsg;
                $PODeventsArray[$offset]['StatusDetails'] = $statusDetails;
                $PODeventsArray[$offset]['TrackingMasterCode'] = NULL;
                $PODeventsArray[$offset]['TrackingMasterEventDescription'] = NULL;
                $PODeventsArray[$offset]['OurEventCode'] = NULL;
                $PODeventsArray[$offset]['EventDescription'] = NULL;
                $PODeventsArray[$offset]['TrackingAPIEvent'] = 'No';
                $PODeventsArray[$offset]['MicroStatus'] = NULL;

                $offset++;
            }
        }
        //our master event table and Tracking Msg
        $trackingEventsMapping = DB::connection('mssql')->select("SELECT TrackingMsg, TrackingMasterCode, OurEventCode, EventDescription FROM TrackingEventMapping");

        //Micro Status table
        $micro_status =  DB::connection('mssql')->select("SELECT DISTINCT Status, MicroStatusName FROM MicroStatusMapping ");
        $micro_status_array = [];
        foreach ($micro_status as $key => $status) {
            $micro_status_array[$status->Status] = $status->MicroStatusName;
        }

        //Amazon master event table
        $trackingEventsMaster = DB::connection('mssql')->select("SELECT TrackingEventCode, EventCodeDescription FROM TrackingEventMaster");

        $trackingEventsMasterArray = [];

        //making associative array for amazon master tracking table according to code and Description
        //eg:- [EVENT_101] => Shipment has been Booked

        foreach ($trackingEventsMaster as $trackingEventMaster) {
            $trackingEventsMasterArray[$trackingEventMaster->TrackingEventCode] = $trackingEventMaster->EventCodeDescription;
        }

        $B2CShipEventsMapping = DB::connection('mssql')->select("SELECT B2CShipMsg, B2CShipSource, IsActive from B2CShipEventMapping");
        $trackingAPIMsg = [];
        foreach ($B2CShipEventsMapping as $B2CShipEventMapping) {

            $B2CShipSource = $B2CShipEventMapping->B2CShipSource;
            if ($B2CShipSource == '') {
                $B2CShipSource = 'B2CShip';
            }
            $B2CShipMsg = ($B2CShipEventMapping->B2CShipMsg);
            // $B2CShipMsg = strtoupper($B2CShipMsg);

            $trackingAPIMsg[$B2CShipSource . ' : ' . $B2CShipMsg] = 'Yes';
        }

        $offset = 0;

        foreach ($PODeventsArray as $PODeventskey => $PODeventArray) {

            foreach ($trackingEventsMapping as $trackingEventMapping) {

                if (strtoupper($PODeventArray['TrackingMsg']) == strtoupper($trackingEventMapping->TrackingMsg)) {

                    $PODeventsArray[$offset]['TrackingMasterCode'] = $trackingEventMapping->TrackingMasterCode;

                    $PODeventsArray[$offset]['TrackingMasterEventDescription'] = $trackingEventsMasterArray[$trackingEventMapping->TrackingMasterCode];

                    $PODeventsArray[$offset]['OurEventCode'] = $trackingEventMapping->OurEventCode;

                    $PODeventsArray[$offset]['EventDescription'] = $trackingEventMapping->EventDescription;
                    // exit;
                }
            }

            $trackingmsg = $PODeventArray['TrackingMsg'];
            if (isset($trackingAPIMsg[$trackingmsg])) {
                $PODeventsArray[$offset]['TrackingAPIEvent'] = $trackingAPIMsg[$trackingmsg];
            }

            $trackingStatusDetails = $PODeventArray['StatusDetails'];
            if (isset($micro_status_array[$trackingStatusDetails])) {

                $PODeventsArray[$offset]['MicroStatus'] = $micro_status_array[$trackingStatusDetails];
            } elseif (str_contains($trackingStatusDetails, 'Shipment has been OUTWARDED With Bag No')) {
                $PODeventsArray[$offset]['MicroStatus'] = 'In Transit - US to India';
            } elseif (str_contains($trackingStatusDetails, 'CLEARANCE PROCEDURE')) {
                $PODeventsArray[$offset]['MicroStatus'] = 'In Transit - Under Custom';
            } elseif (str_contains($trackingStatusDetails, 'Delivery date rescheduled')) {
                $PODeventsArray[$offset]['MicroStatus'] = '	In Transit - Lastmile';
            }
            $offset++;
        }
        // po($PODeventsArray);
        // exit;
        return $PODeventsArray;
    }


    public function microStatusMissingReport(Request $request)
    {
        if ($request->ajax()) {

            $PODeventsArray = [];
            $offset = 0;

            $PODtransEvents = DB::connection('mssql')->select("SELECT DISTINCT StatusDetails, FPCode FROM PODTrans ");

            foreach ($PODtransEvents as $PODtransEvent) {
                $fpCode = $PODtransEvent->FPCode;
                $statusDetails = $PODtransEvent->StatusDetails;
                if ($fpCode == '') {
                    $fpCode = 'B2CShip';
                }
                $ignorebombion = '[BOMBINO]';
                $trackingMsg = $fpCode . ' : ' . $statusDetails;
                if ((!str_contains($trackingMsg, $ignorebombion))) {
                    $PODeventsArray[$offset]['TrackingMsg'] = $trackingMsg;
                    $PODeventsArray[$offset]['StatusDetails'] = $statusDetails;

                    $offset++;
                }
            }

            $micro_status =  DB::connection('mssql')->select("SELECT DISTINCT Status, MicroStatusName FROM MicroStatusMapping ");
            $micro_status_array = [];
            foreach ($micro_status as $key => $status) {
                $micro_status_array[strtoupper($status->Status)] = strtoupper($status->MicroStatusName);
            }
            $micro_status_missing = [];
            $ms_offset = 0;

            foreach ($PODeventsArray as $PODevnetKey => $tracking) {
                $tracking_msg = trim(strtoupper($tracking['StatusDetails']));

                if (!isset(($micro_status_array[$tracking_msg])) && !str_contains($tracking_msg, strtoupper('Shipment has been OUTWARDED With Bag No')) && !str_contains($tracking_msg, strtoupper(' CLEARANCE PROCEDURE IN PROGRESS Description: CLEARANCE PROGRESS')) && !str_contains($tracking_msg, strtoupper('Delivery date rescheduled'))) {

                    $micro_status_missing[$ms_offset]['Status'] = $tracking_msg;
                    $micro_status_missing[$ms_offset]['Tracking_msg'] = $tracking['TrackingMsg'];
                    $ms_offset++;
                }
            }
            return DataTables::of($micro_status_missing)
                ->addIndexColumn()
                ->make(true);
        }
        // dd($micro_status_missing);
        return view('b2cship.trackingStatus.micro_status_missing_report');
    }

    public function microStatusReport()
    {
        $today_sd = Carbon::today();
        $today_ed = Carbon::now();

        $yesterday_sd = Carbon::yesterday();
        $yesterday_ed = $yesterday_sd->toDateString();
        $yesterday_ed = $yesterday_ed . ' 23:59:59';

        $last7day_sd = Carbon::today()->subDays(7);
        $last7day_ed = $yesterday_ed;

        $last30day_sd = Carbon::today()->subDays(30);
        $last30day_ed = $yesterday_ed;
        $file_path = "MicroStatusJson/microstatus_details.json";

        $micro_status_today_count = [];
        $micro_status_yesterday_count = [];
        $micro_status_7_days_count = [];
        $micro_status_30_days_count = [];
        $offset_7_days = 0;
        $offset_yesterdays = 0;
        $offset = 0;

        $micro_status_mapping = DB::connection('mssql')->select("SELECT DISTINCT  MicroStatusCode, Status, MicroStatusName FROM MicroStatusMapping");
        $micro_status_name = [];
        foreach ($micro_status_mapping as $micro_status_value) {
            $micro_status_name[$micro_status_value->MicroStatusCode] = $micro_status_value->MicroStatusName;

            $micro_status[$micro_status_value->Status] = $micro_status_value->MicroStatusName;
        }
        if (!Storage::exists($file_path)) {

            $micro_status_final_array[] = [
                'Today' => Null,
                'Yesterday' => NULL,
                'Last7days' => NULL,
                'Last30days' => NULL,
            ];
            return view('b2cship.trackingStatus.micro_status_report', compact(['micro_status_final_array']));
        }

        $packet_status = DB::connection('mssql')->select("SELECT DISTINCT
            AwbNo, StatusDetails, CreatedDate 
            FROM PODTrans 
            WHERE CreatedDate BETWEEN '$today_sd' AND '$today_ed'
            ORDER BY CreatedDate DESC
            ");
        $packet_status_details = collect($packet_status);
        $packet_status = $this->packet_status($packet_status_details, $today_sd, $today_ed);
        $status_count_today = $this->micro_status_count($micro_status, $packet_status);

        $micro_staus_jd = Storage::get($file_path);

        $data = json_decode($micro_staus_jd);
        $status_count_yesterday = (array)$data->yesterday;
        $status_count_last_7day = (array)$data->last7days;
        $status_count_last_30day = (array)$data->last30days;

        $micro_status_final_array = [];
        foreach ($micro_status as $micro_status_key => $micro_status_value) {
            $micro_status_key = trim($micro_status_key);
            $micro_status_value = trim($micro_status_value);
            $today_value = 0;
            $yesterday_value = 0;
            $last7day_value = 0;
            $last30day_value = 0;
            $micro_status_value = trim($micro_status_value);
            if (isset($status_count_today[$micro_status_value])) {

                $today_value = $status_count_today[$micro_status_value];
            }
            if (isset($status_count_yesterday[$micro_status_value])) {

                $yesterday_value = $status_count_yesterday[$micro_status_value];
            }
            if (isset($status_count_last_7day[$micro_status_value])) {

                $last7day_value = $status_count_last_7day[$micro_status_value];
            }
            if (isset($status_count_last_30day[$micro_status_value])) {

                $last30day_value = $status_count_last_30day[$micro_status_value];
            }
            $micro_status_final_array[$micro_status_value] = [
                'Today' => $today_value,
                'Yesterday' => $yesterday_value,
                'Last7days' => $last7day_value,
                'Last30days' => $last30day_value,
            ];
        }

        // po($micro_status_final_array);
        return view('b2cship.trackingStatus.micro_status_report', compact(['micro_status_final_array']));
    }

    public function packet_status($packet_status_details, $start_date, $end_date)
    {
        $packet_detials = $packet_status_details->whereBetween('CreatedDate', [$start_date, $end_date])
            ->groupBy('StatusDetails')
            ->map(function ($row) {
                return $row->count();
            });
        $shipment_status_count = [];
        foreach ($packet_detials as $key => $value) {
            $key = trim($key);
            if (isset($shipment_status_count[$key])) {
                $shipment_status_count[$key] +=  $value;
            } else {
                $shipment_status_count[$key] = $value;
            }
        }
        return $shipment_status_count;
    }

    public function micro_status_count($micro_status, $packet_status)
    {
        $status_count = [];
        foreach ($micro_status as $micro_status_key => $micro_status_value) {
            $micro_status_key = trim($micro_status_key);
            $micro_status_value = trim($micro_status_value);
            if (isset($packet_status[$micro_status_key])) {
                if (isset($status_count[$micro_status_value])) {
                    $status_count[$micro_status_value] += $packet_status[$micro_status_key];
                } else {
                    $status_count[$micro_status_value] = $packet_status[$micro_status_key];
                }
            }
        }

        return $status_count;
    }

    public function update_report()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            
            // exec('nohup php artisan pms:textiles-import  > /dev/null &');
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:microstatus-report > /dev/null &";
            exec($command);
            
        } else {

            Artisan::call('pms:microstatus-report');
        }

        // $this->microStatusReport();
        return redirect()->back();
    }
}
