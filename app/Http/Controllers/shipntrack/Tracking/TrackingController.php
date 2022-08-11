<?php

namespace App\Http\Controllers\shipntrack\Tracking;

use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function Tracking(Request $request)
    {
        $tracking_no = 'US30000001';
        $bombino_t_details  = [];
        $smsa_t_detials = [];

        $packet_forwarder = PacketForwarder::where('awb_no', $tracking_no)->get()->first();

        $forwarder_1 = $packet_forwarder->forwarder_1;
        $forwarder_1_awb = $packet_forwarder->forwarder_1_awb;

        $forwarder_2 = $packet_forwarder->forwarder_2;
        $forwarder_2_awb = $packet_forwarder->forwarder_2_awb;

        if (strtoupper($forwarder_1) == 'BOMBINO') {

            $bombino_t_details = bombino_tracking($forwarder_1_awb);
        } elseif (strtoupper($forwarder_1) == "SMSA") {

            $smsa_t_detials = smsa_tracking($forwarder_1_awb);
        }

        if (strtoupper($forwarder_2) == 'BOMBINO') {

            $bombino_t_details = bombino_tracking($forwarder_2_awb);
        } elseif (strtoupper($forwarder_2_awb) == "SMSA") {

            $smsa_t_detials = smsa_tracking($forwarder_2_awb);
        }

        $tracking_details = [...$bombino_t_details, ...$smsa_t_detials];

        $column = array_column($tracking_details, 'Date_Time');
        array_multisort($column, SORT_DESC, $tracking_details);

        po($tracking_details);
        exit;
    }
}
