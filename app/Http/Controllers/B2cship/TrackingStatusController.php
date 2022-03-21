<?php

namespace App\Http\Controllers\B2cship;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TrackingStatusController extends Controller
{
    public function trackingStatusDetails()
    {   $PODeventsArray =[];
        $offset = 0;

        $PODtransEvents = DB::connection('mssql')->select("SELECT DISTINCT ISNULL(FPCode,'B2CShip')+ ' : ' + StatusDetails AS TrackingMsg FROM PODTrans");
        
        // Making By default null for every code and description
        foreach ($PODtransEvents as $PODtransEvent) {

            $PODeventsArray[$offset]['TrackingMsg'] = $PODtransEvent->TrackingMsg;
            $PODeventsArray[$offset]['TrackingMasterCode'] = NULL;
            $PODeventsArray[$offset]['TrackingMasterEventDescription'] = NULL;
            $PODeventsArray[$offset]['OurEventCode'] = NULL;
            $PODeventsArray[$offset]['EventDescription'] = NULL;
            $offset++;
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

        $offset = 0;

        foreach ($PODeventsArray as $PODeventskey => $PODeventArray) {
            
            foreach ($trackingEventsMapping as $trackingEventMapping) {
               
                if ($PODeventArray['TrackingMsg'] == $trackingEventMapping->TrackingMsg) {

                    $PODeventsArray[$offset]['TrackingMasterCode'] = $trackingEventMapping->TrackingMasterCode;
                   
                    $PODeventsArray[$offset]['TrackingMasterEventDescription'] = $trackingEventsMasterArray[$trackingEventMapping->TrackingMasterCode];
                   
                    $PODeventsArray[$offset]['OurEventCode'] = $trackingEventMapping->OurEventCode;

                    $PODeventsArray[$offset]['EventDescription'] = $trackingEventMapping->EventDescription;

                    break;
                }
            }
            $offset++;
        }
        po($PODeventsArray);
    }
}
