<?php

namespace App\Http\Controllers\shipntrack\OutwardManisist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Courier\CourierPartner;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class ShiptrackInScanController extends Controller
{
    public function  index()
    {

        return view('shipntrack.manifist.inscan.index');
    }
    public function  view()
    {
        $destinations = CourierPartner::select('source', 'destination')
            ->where('type', 1)
            ->groupBy('source', 'destination')
            ->get()
            ->toArray();
        return view('shipntrack.manifist.inscan.add', compact('destinations'));
    }
    public function get_details(Request $request)
    {

        $data = '';
        if ($request->mode == 'AE') {
            $data =  Trackingae::where('purchase_tracking_id', $request->awb)->get();
        } elseif ($request->mode == 'IN') {
            $data =    Trackingin::where('purchase_tracking_id', $request->awb)->get();
        } elseif ($request->mode == 'KSA') {
            $data =  Trackingksa::where('purchase_tracking_id', $request->awb)->get();
        }
    
        $data = (json_decode($data));
        $awb = $data[0]->awb_no;
        $ref_id = $data[0]->reference_id;
        $purchase_tracking_id = $data[0]->purchase_tracking_id;
        $consignor_details = json_decode($data[0]->consignor_details);
        $consignee_details = json_decode($data[0]->consignee_details);
        $packet_details =  json_decode($data[0]->packet_details);
        $shipping_details =  ($data[0]->shipping_details);
        $booking_details = json_decode($data[0]->booking_details);

        $response = [
            'mode' => $request->mode,
            'awb' => $awb,
            'ref_id' => $ref_id,
            'purchase_tracking_id' => $purchase_tracking_id,
            'order_id' => $booking_details->order_id,
            'item_id' => $booking_details->item_id,
            'booking_date' => $booking_details->booking_date,
            'consignee' => $consignee_details->consignee,
            'consignor' => $consignor_details->consignor,
            'item_name' => $packet_details->pkt_name,
        ];
        return response()->json($response);
    }
    public function store(Request $request)
    {
        Log::alert($request->all());
    }
}
