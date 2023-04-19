<?php

namespace App\Http\Controllers\shipntrack\Operations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Inventory\Outward;
use App\Models\ShipNTrack\Courier\CourierPartner;

class SNTOutwardController extends Controller
{
    public function index(Request $request)
    {

        $data = Outward::query()->get();
        if ($request->ajax()) {

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    if ($row->type == 1) {
                        return 'Outward form source';
                    } else   if ($row->type == 2) {
                    return 'Outward form Destination';
                    } else {
                    return 'Unknown Status';
                    }
                })
                // ->addColumn('action', function ($row) {
                //     return 'laa';
                // })

                // ->rawColumns([ 'action'])
                ->make(true);
        }

        return view('shipntrack.Operation.Outward.index');
    }
    public function outward_view(Request $request)
    {
        $destinations = CourierPartner::select('source', 'destination')
            ->where('type', 1)
            ->groupBy('source', 'destination')
            ->get()
            ->toArray();
        return view('shipntrack.Operation.Outward.outward', compact('destinations'));
    }
    public function outward_store(Request $request)
    {

        start:
        $uniq = random_int(1000, 99999);
        $ship_id = 'OUT' . $uniq;

        $items = [];
        $val = Outward::query()
            ->select(('manifest_id'))
            ->where('manifest_id', $ship_id)
            ->first();

        if ($val) {
            goto start;
        }
        foreach ($request->awb as $key => $awb) {

            $data = [
                'manifest_id' => $ship_id,
                'mode' => $request->mode[$key],
                'type' => $request->type[$key],
                'awb_number' => $awb,
                'status' => $request->status[$key]
            ];
            Outward::upsert($data, ['awb_number_status_unique'], ['status']);
        }
        return response()->json(['success' => 'Shipment has Created successfully']);
    }
}
