<?php

namespace App\Http\Controllers\shipntrack\Manifest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Inventory\In_Scan;
use App\Models\ShipNTrack\Courier\CourierPartner;
use App\Models\ShipNTrack\Process\Process_Master;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class ShiptrackInScanController extends Controller
{
    public function  index(Request $request)
    {

        $destinations  = Process_Master::query()->get();

        return view('shipntrack.manifest.inscan.add', compact('destinations'));
    }

    public function get_details(Request $request)
    {
        $receved_data = explode('_', $request->mode);
        $mode = ($receved_data[1]);
        $data = '';
        if ($mode == 'AE') {
            $data =  Trackingae::where('purchase_tracking_id', $request->awb)->get();
        } elseif ($mode == 'IN') {
            $data =    Trackingin::where('purchase_tracking_id', $request->awb)->get();
        } elseif ($mode == 'SA') {
            $data =  Trackingksa::where('purchase_tracking_id', $request->awb)->get();
        }
        if (count($data) > 0) {


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
            return response()->json(['success' => 'success', 'data' => $response]);
        } else {
            return response()->json(["error" => 'No data Found..Please Check The ID Enterd']);
        }
    }
    public function store(Request $request)
    {
        $mode_array = explode('_', $request->mode);
        $val = In_Scan::query()
            ->select(('manifest_id'))
            ->where('destination', $mode_array[0])
            ->orderBy('created_at', 'desc')->first();

        if ((!$val)) {
            $ship_id = $mode_array[1] . $mode_array[2] . '100001';
        } else {

            $existing_id = substr($val->manifest_id, 4);
            $new_id = $existing_id + 1;
            $ship_id = $mode_array[1] . $mode_array[2] . $new_id;
        }
        foreach ($request->awb as $key => $awb) {

            $data = [
                'manifest_id' => $ship_id,
                'destination' => $mode_array[0],
                'awb_number' => $awb,
                'purchase_tracking_id' => $request->tracking[$key],
                'order_id' => $request->Order_id[$key],

            ];
            In_Scan::create($data);
        }

        return response()->json(['success' => 'Shipment has Created successfully']);
        // return redirect()->route('shipntrack.inscan')->with('success', 'Shipment has been created successfully');
    }
}
