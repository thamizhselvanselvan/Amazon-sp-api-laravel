<?php

namespace App\Http\Controllers\shipntrack\API;

use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\EventMapping\TrackingEventMapping;
use App\Services\ShipNTrack\API\AmazonTrackingRequest;

class AmazonTrackingAPIController extends Controller
{

    public function AmazonTrackingMaster(Request $request)
    {
        $requestContent = $request->getContent();

        $tracking_request = new AmazonTrackingRequest();

        return $tracking_request->TrackingMaster($requestContent);
    }
}
