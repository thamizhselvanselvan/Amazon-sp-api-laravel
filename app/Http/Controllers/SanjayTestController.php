<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ShipNTrack\Courier\Courier;
use App\Models\ShipNTrack\Courier\StatusManagement;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Models\ShipNTrack\CourierTracking\AramexTracking;
use App\Models\ShipNTrack\CourierTracking\BombinoTracking;

class SanjayTestController extends Controller
{
    public function index()
    {
        po('here');
    }
}
