<?php

namespace App\Http\Controllers\shipntrack\Tracking;

use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Courier\CourierPartner;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use App\Models\ShipNTrack\Packet\StopPacketTracking;
use App\Models\ShipNTrack\Packet\StopTrackingShowEvent;
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
        $courier_partner_array = ['BO', 'SA', 'EP', 'AM'];
        $courier_partner = CourierPartner::get(['name', 'courier_code']);

        if ($request->ajax()) {

            $source = $request->source;

            $key = array_search($source, $courier_partner_array);

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
                                <td> <input type='checkbox' name = 'show[]' value ='$data'/></td>
                                <td> <input type='checkbox' name = 'stop[]' value ='$data'/> </td>
                              <tr>";
                }
            }
            return ['success' => $html];
        }
        return view('shipntrack.Tracking.stopTracking', compact('courier_partner'));
    }

    public function StopTrackingUpdate(Request $request)
    {
        $show_array = explode('-_~_-', $request->show);
        $stop_array = explode('-_~_-', $request->stop);

        $forwarder = $request->forwarder;

        StopTrackingShowEvent::where('forwarder_code', $forwarder)
            ->update(['show_tracking' => '0', 'stop_tracking' => '0']);

        $update_show =  [];
        $update_stop = [];
        foreach ($show_array as $key => $value) {
            $update_show[] = [
                'forwarder_code' => $forwarder,
                'event' => $value,
                'show_tracking' => '1'
            ];
        }

        foreach ($stop_array as $key => $value) {
            $update_stop[] = [
                'forwarder_code' => $forwarder,
                'event' => $value,
                'stop_tracking' => '1'
            ];
        }

        StopTrackingShowEvent::upsert(
            $update_show,
            'forwarder_event_unique',
            [
                'forwarder_code',
                'event',
                'show_tracking',
            ]
        );

        StopTrackingShowEvent::upsert(
            $update_stop,
            'forwarder_event_unique',
            [
                'forwarder_code',
                'event',
                'stop_tracking',
            ]
        );

        return redirect()->back()->with('success', 'Stop Tracking Updated');
    }
}
