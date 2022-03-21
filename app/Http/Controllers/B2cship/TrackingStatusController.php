<?php

namespace App\Http\Controllers\B2cship;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TrackingStatusController extends Controller
{
    public function trackingStatusDetails()
    {
        $PODtransEvents = DB::connection('mssql')->select("SELECT DISTINCT ISNULL(FPCode,'B2CShip')+ ' : ' + StatusDetails AS TrackingMsg FROM PODTrans");

        foreach ($PODtransEvents as $PODtransEvent) {

            $PODeventsArray[] = $PODtransEvent->TrackingMsg;
        }
        $trackingEventsMapping = DB::connection('mssql')->select("SELECT TrackingMsg, TrackingMasterCode, OurEventCode, EventDescription FROM TrackingEventMapping");
        
        $trackingEventsMaster = DB::connection('mssql')->select("SELECT TrackingEventCode, EventCodeDescription FROM TrackingEventMaster");
        
        po($trackingEventsMaster);
       
    
    }
}
