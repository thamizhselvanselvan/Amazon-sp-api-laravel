<?php

namespace App\Http\Controllers\B2cship;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\ToArray;
use Yajra\DataTables\Facades\DataTables;

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

    public function microStatusReport(Request $request)
    {
        $today_start_date = Carbon::today();
        $today_end_date = Carbon::now();

        $yesterday_start_date = Carbon::yesterday();
        $yesterday_end_date = $yesterday_start_date->toDateString();
        $yesterday_end_date = $yesterday_end_date . ' 23:59:59';


        $last7day_start_date = Carbon::today()->subDays(7);
        // echo $yesterday_start_date;
        // echo "<br>";
        $last7day_end_date = $yesterday_end_date;

        $last30day_start_date = Carbon::today()->subDays(30);
        $last30day_end_date = $yesterday_end_date;

        // echo $last30day_start_date;
        // echo ' End Time-> ' . $last30day_end_date;

        $micro_status_mapping = DB::connection('mssql')->select("SELECT DISTINCT Status, MicroStatusCode, MicroStatusName FROM MicroStatusMapping");
        $micro_status_name = [];
        foreach ($micro_status_mapping as $micro_status_value) {
            $micro_status_name[$micro_status_value->MicroStatusCode] = $micro_status_value->MicroStatusName;

            $micro_status[$micro_status_value->Status] = $micro_status_value->MicroStatusName;
        }

        $packet_status = DB::connection('mssql')->select("SELECT DISTINCT TOP 2000
         AwbNo, StatusDetails, CreatedDate 
         FROM PODTrans 
         WHERE CreatedDate BETWEEN '$last30day_start_date' AND '$today_end_date'
         ORDER BY CreatedDate DESC
         ");

        $packet_status = collect($packet_status);
        $packet_status = $packet_status->groupBy('AwbNo');
        $count = 0;
        foreach ($packet_status as $status) {
            $pdo_status[$count]['AwbNo'] = $status[0]->AwbNo;
            $pdo_status[$count]['StatusDetails'] = $status[0]->StatusDetails;
            $pdo_status[$count]['CreatedDate'] = $status[0]->CreatedDate;
            $count++;
        }
        // dd($micro_status, $packet_status);
        $pdo_status_30_days = $pdo_status;
        $pdo_status_7_days = [];
        $pdo_status_yesterdays = [];
        $micro_status_yesterday_count = [];
        $micro_status_7_days_count = [];
        $micro_status_30_days_count = [];
        $offset_7_days = 0;
        $offset_yesterdays = 0;
        $offset = 0;

        // po($pdo_status);
        // exit;
        foreach ($pdo_status as $pdo_value) {
            $create_date = $pdo_value['CreatedDate'];
            foreach ($micro_status as $key => $micro_status_value) {
                if (($pdo_value['StatusDetails']) == $key) {
                    //last 30 days details;
                    if ($create_date <= $last30day_end_date && $create_date >= $last30day_start_date) {

                        if (isset($micro_status_30_days_count[$micro_status_value])) {
                            $micro_status_30_days_count[$micro_status_value] += 1;
                        } else {
                            $micro_status_30_days_count[$micro_status_value] = 1;
                        }
                    }
                    //last 7 days details;
                    if ($create_date <= $last7day_end_date && $create_date >= $last7day_start_date) {

                        if (isset($micro_status_7_days_count[$micro_status_value])) {
                            $micro_status_7_days_count[$micro_status_value] += 1;
                        } else {
                            $micro_status_7_days_count[$micro_status_value] = 1;
                        }
                    }
                    //yesterday details 
                    if ($create_date >= $yesterday_end_date && $create_date <= $yesterday_start_date) {

                        if (isset($micro_status_yesterday_count[$micro_status_value])) {
                            $micro_status_yesterday_count[$micro_status_value] += 1;
                        } else {
                            $micro_status_yesterday_count[$micro_status_value] = 1;
                        }
                    }
                }
            }
        }
        // dd('yesterday', $micro_status_yesterday_count, '7 days', $micro_status_7_days_count, '30 days', $micro_status_30_days_count);

        $micro_status_final_array = [];
        foreach ($micro_status as $micro_status_key => $micro_status_value) {

            $yesterday_value = NULL;
            $last7day_value = NULL;
            $last30day_value = NULL;

            if (isset($micro_status_yesterday_count[$micro_status_value])) {

                $yesterday_value = $micro_status_yesterday_count[$micro_status_value];
            }
            if (isset($micro_status_7_days_count[$micro_status_value])) {

                $last7day_value = $micro_status_7_days_count[$micro_status_value];
            }
            if (isset($micro_status_30_days_count[$micro_status_value])) {

                $last7day_value = $micro_status_30_days_count[$micro_status_value];
            }
            $micro_status_final_array[$micro_status_value] = [
                
                'Yesterday' => $yesterday_value,
                'Last7days' => $last7day_value,
                'Last30days' => $last30day_value,
            ];
        }
        // dd($micro_status_final_array);
        return view('b2cship.trackingStatus.micro_status_report', compact(['micro_status_final_array']));
    }
}
