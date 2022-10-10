<?php

namespace App\Http\Controllers\shipntrack\Tracking;

use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use App\Models\ShipNTrack\Packet\StopPacketTracking;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function Tracking(Request $request)
    {
        $tracking_no = 'US30000001';
        $tracking_details = getTrackingDetails($tracking_no);

        po($tracking_details);
        exit;
    }

    public function StopTracking(Request $request)
    {
        $courier_partner = ['Bombino', 'Smsa', 'EmiratePost', 'Aramex'];

        if ($request->ajax()) {

            $source = $request->source;

            $key = array_search($source, $courier_partner);

            $details =  forwarderTrackingEvent($key);

            $table_model = $details[0];
            $table_column = $details[1];

            $html = '';
            $data = $table_model::get()->unique($table_column);
            foreach ($data as  $value) {
                $data = $value->$table_column;
                if ($data) {
                    $html  .= "<tr>
                                <td class='text-left'> $data </td>
                                <td> <input type='checkbox' value ='$data'/></td>
                                <td> <input type='checkbox' value ='$data'/> </td>
                              <tr>";
                }
            }
            return ['success' => $html];
        }
        return view('shipntrack.Tracking.stopTracking', compact('courier_partner'));
    }

    public function StopTrackingUpdate(Request $request)
    {
        $forwarder = $request->forwarder;
        $tracking_status = $request->tracking_status;

        StopPacketTracking::upsert(
            [
                'forwarder' => $forwarder,
                'tracking_status' => $tracking_status
            ],
            'forwarder_status_unique'
        );

        return redirect()->back()->with('success', 'Stop Tracking Updated');
    }
}
