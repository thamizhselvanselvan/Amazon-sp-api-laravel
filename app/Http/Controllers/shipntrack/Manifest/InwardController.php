<?php

namespace App\Http\Controllers\shipntrack\Manifest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Inventory\Inward;
use App\Models\ShipNTrack\Process\Process_Master;
use App\Models\ShipNTrack\Inventory\Manifest_Master;
use App\Models\ShipNTrack\Inventory\Inwarding_detail;
use App\Models\ShipNTrack\Inventory\Manifest_Item;

class InwardController extends Controller
{
    public function index(Request $request)
    {
        $data = Inward::query()->get();
        if ($request->ajax()) {

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex"><a href="/shipntrack/inward/' . $row->id . '/view" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> View</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipntrack.manifest.inward.index');
    }

    public function inw_view(Request $request)
    {
        $destinations  = Process_Master::query()->get();
        return view('shipntrack.manifest.inward.add', compact('destinations'));
    }
    public function inw_data_fech(Request $request)
    {
        $awb = $request->awb;
        $mode = $request->mode;
        $explode = explode('_', $mode);
        $destination = $explode[1];
        $response = DB::connection('shipntracking')->table('manifest_masters')
            ->where('manifest_masters.international_awb_number', $awb)
            ->where('manifest_items.destination', $destination)
            ->rightJoin('manifest_items', 'manifest_masters.international_awb_number', '=', 'manifest_items.international_awb_number')
            ->select('manifest_masters.total_items', 'manifest_items.*')
            ->get();


        if (count($response) > 0) {
            return response()->json(['success' => 'success', 'data' =>   $response]);
        } else {
            return response()->json(["error" => 'No data Found..Please Check The ID Enterd']);
        }
    }

    public function store(Request $request)
    {

        $mode_received = explode('_', $request->mode);
        $mode = $mode_received[0];

        $val = Inward::query()
            ->select(('shipment_id'))
            ->orderBy('created_at', 'desc')->first();

        if ((!$val)) {
            $ship_id = $mode_received[1]  . '10000001';
        } else {
            $existing_id = substr($val->shipment_id, 2);
            $new_id = $existing_id + 1;
            $ship_id = $mode_received[1] . $new_id;
        }
        foreach ($request->awb as $key => $awb) {
            $awb_array = [];
            $data = $request->status[$key];

            if ($data == '-YES-') {
                $awb_array[] = $awb;
            }
        }


        $data_inwarding = [
            'shipment_id' =>   $ship_id,
            'total_items_in_export' => $request->total_item,
            'total_items_receved' =>   count($awb_array),

            'international_awb_number' =>  $request->international_awb_number,
        ];
        Inward::create($data_inwarding);


        $id_query = Inward::where(['shipment_id' =>   $ship_id])->select('id')->get()->toarray();

        $id = ($id_query[0]['id']);

        foreach ($request->awb as $key => $awb) {
            $staus = null;
            $data = $request->status[$key];

            if ($data === '-YES-') {
                $staus = 1;
            } else if ($data === '-NO-') {
                $staus = 0;
            }

            $data = [
                'awb_number' => $awb,
                'master_ref_id' => $id,
                'shipment_id' => $ship_id,
                'mode' => $mode,
                'total_items_in_export' => $request->total_item,
                'total_items_receved' => count($awb_array),
                'international_awb_number' =>  $request->international_awb_number,
                'purchase_tracking_id' =>  $request->purchase_tracking_id[$key],
                'order_id' => $request->Order_id[$key],
                'item_received_status' => $staus,

            ];
            Inwarding_detail::create($data);
        }
        return response()->json(['success' => 'Shipment has Created successfully']);
    }

    public function verify(Request $request)
    {
        $awb = $request->awb;
        $mode = $request->mode;
        $explode = explode('_', $mode);
        $destination = $explode[1];
        $response = Manifest_Item::where('awb', $awb)->get();

        if (count($response) > 0) {
            return response()->json(['success' => 'success', 'data' =>   $response]);
        } else {
            return response()->json(["error" => 'No data Found..Please Check The ID Enterd']);
        }
    }
}
