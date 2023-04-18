<?php

namespace App\Http\Controllers\ShipNTrack\Operations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Courier\CourierPartner;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class SNTInwardController extends Controller
{
    public function index(Request $request)
    {
        return view('shipntrack.Operation.inward.index');
    }
    public function inward_view(Request $request)
    {
        $destinations = CourierPartner::select('source', 'destination')
            ->where('type', 1)
            ->groupBy('source', 'destination')
            ->get()
            ->toArray();


        if ($request->ajax()) {
            $awbs = preg_split('/[\r\n| |:|,]/', $request->awb, -1, PREG_SPLIT_NO_EMPTY);
            $mode = $request->mode;

            if ($mode == 'AE') {
                $datas = Trackingae::with('CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4')->whereIn('awb_number', $awbs)->get();
            } elseif ($mode == 'IN') {
                $datas = Trackingin::with('CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4')->whereIn('awb_number', $awbs)->get();
            } elseif ($mode == 'KSA') {
                $datas = Trackingksa::with('CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4')->whereIn('awb_number', $awbs)->get();
            }

            return  response()->json($datas);
        }
        return view('shipntrack.Operation.inward.inward', compact('destinations'));
    }
    public function inward_store(Request $request)
    {
        if ($request->ajax()) {
            foreach ($request->awb as $key => $awb) {
                Log::info($awb);
                Log::info($request->mode);
                
            }
            return response()->json(['success' => 'Shipment has Created successfully']);
             
        }
    }
}
