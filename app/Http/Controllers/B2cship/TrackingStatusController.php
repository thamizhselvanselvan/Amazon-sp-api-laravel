<?php

namespace App\Http\Controllers\B2cship;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class TrackingStatusController extends Controller
{    public $micro_status= NULL;
    public function trackingStatusDetails(Request $request)
    {
        $this->micro_status =  DB::connection('mssql')->select("SELECT DISTINCT Status, MicroStatusName FROM MicroStatusMapping ");
        
        
        // dd($this->micro_status);

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
            if ((!str_contains($trackingMsg, $ignorebombion))&&(!str_contains($trackingMsg, $ignoreDelivered)) && (!str_contains($trackingMsg, $ignoreOFD)) && (!str_contains($trackingMsg, $ignoreRunNO)) && (!str_contains($trackingMsg, $ignoreUnDelivered))) {
                
                $PODeventsArray[$offset]['TrackingMsg'] = $trackingMsg;
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
            $offset++;
        }
        // po($PODeventsArray);
        // exit;
        return $PODeventsArray;
    }
}
