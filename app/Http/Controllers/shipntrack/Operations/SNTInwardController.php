<?php

namespace App\Http\Controllers\ShipNTrack\Operations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Inventory\Inward;
use App\Models\ShipNTrack\Courier\CourierPartner;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class SNTInwardController extends Controller
{
    public function index(Request $request)
    {
        $data = Inward::query()->get();
        if ($request->ajax()) {

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    if ($row->type == 1) {
                        return 'Inward In source.';
                    } else   if ($row->type == 2) {
                        return 'Inward In Destination.';
                    } else {
                        return 'Unknown Status.';
                    }
                })
                // ->addColumn('action', function ($row) {
                //     return 'laa';
                // })

                // ->rawColumns([ 'action'])
                ->make(true);
        }
        return view('shipntrack.Operation.inward.index');
    }
    public function inward_view(Request $request)
    {
        $destinations = CourierPartner::select('source', 'destination')
            ->where('type', 1)
            ->groupBy('source', 'destination')
            ->get()
            ->toArray();

        return view('shipntrack.Operation.inward.inward', compact('destinations'));
    }
    public function inward_store(Request $request)
    {
      
        start:
        $uniq = random_int(1000, 99999);
        $ship_id = 'INW' . $uniq;

        $items = [];
        $val = Inward::query()
            ->select(('manifest_id'))
            ->where('manifest_id', $ship_id)
            ->first();

        if ($val) {
            goto start;
        }
        foreach ($request->awb as $key => $awb) {
           Log::alert($request->type[$key]);
            $data = [
                'manifest_id' => $ship_id,
                'mode' => $request->mode[$key],
                'type' => $request->type[$key],
                'awb_number' => $awb,
                'status' => $request->status[$key]
            ];
            Inward::upsert($data, ['awb_number_status_unique'], ['status']);
        }

        return response()->json(['success' => 'Shipment has Created successfully']);
    }
}
