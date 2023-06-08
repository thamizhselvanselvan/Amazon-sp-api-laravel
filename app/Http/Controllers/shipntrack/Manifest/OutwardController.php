<?php

namespace App\Http\Controllers\shipntrack\Manifest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Process\Process_Master;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;
use App\Models\ShipNTrack\Inventory\Inwarding_detail;
use App\Models\ShipNTrack\Inventory\Outwarding;

class OutwardController extends Controller
{
    public function index(Request $request)
    {
        $destinations  = Process_Master::query()->get();
        return view('shipntrack.manifest.outward.add', compact('destinations'));
    }

    public function fetch_data(Request $request)
    {
        $awb = $request->awb;
        $mode = $request->mode;
        $explode = explode('_', $mode);
        $destination = $explode[1];

        $destination = strtolower($destination);
        $response = DB::connection("shipntracking")->table("inwarding_details")
            ->where("inwarding_details.awb_number", $awb)
            ->where("inwarding_details.outward_status", 0)
            ->Join("tracking_{$destination}s", "tracking_{$destination}s.purchase_tracking_id", "=", "inwarding_details.purchase_tracking_id")
            ->select("inwarding_details.*", "tracking_{$destination}s.*")
            ->get();

        $response = json_decode($response);
        if (count($response) > 0) {
            $consignor_details = json_decode($response[0]->consignor_details);
            $consignee_details = json_decode($response[0]->consignee_details);
            $packet_details = json_decode(($response[0]->packet_details));
            $consignor = (($consignor_details->consignor));
            $consignee = (($consignee_details->consignee));
            $pkt_name = (($packet_details->pkt_name));



            $data = [
                'awb_number'  => $response[0]->awb_number,
                'purchase_tracking_id'  => $awb,
                'order_id' => $response[0]->order_id,
                'consignor' => $consignor,
                'consignee' => $consignee,
                'packet' => $pkt_name,
                'purchase_tracking_id' => $response[0]->purchase_tracking_id,

            ];
            return response()->json(['success' => 'success', 'data' =>    $data]);
        } else {
            return response()->json(["error" => 'No data Found..Please Check The ID Enterd']);
        }
    }

    public function store(Request $request)
    {
        $mode_array = explode('_', $request->mode);
        $mode = $mode_array[1];

        foreach ($request->awb as $key => $awb) {

            $items = [
                'awb_number' =>  $awb,
                'order_id' =>  $request->Order_id[$key],
                'mode' => $mode_array[0],
                'purchase_tracking_id' => $request->purchase_tracking_id[$key],
                'forwarder_2' =>  $request->forwarder_2,
                'forwarder_2_awb' => $request->forwarder_2_awb,
            ];
            Outwarding::create($items);

            Inwarding_detail::where('awb_number', $awb)->update(['outward_status' => 1]);
            /* Updatwe Forwarder */
            $insert_data = [
                'forwarder_2' => $request->forwarder_2,
                'forwarder_2_awb' => $request->forwarder_2_awb,
                'forwarder_2_flag' => 0,
            ];

            if ($mode == 'AE') {
                Trackingae::where('purchase_tracking_id', $request->purchase_tracking_id[$key])->update($insert_data);
            } elseif ($mode == 'IN') {
                Trackingin::where('purchase_tracking_id', $request->purchase_tracking_id[$key])->update($insert_data);
            } elseif ($mode == 'SA') {
                Trackingksa::where('purchase_tracking_id', $request->purchase_tracking_id[$key])->update($insert_data);
            }
        }
        return response()->json(['success' => 'Shipment has Created successfully']);
    }
}
