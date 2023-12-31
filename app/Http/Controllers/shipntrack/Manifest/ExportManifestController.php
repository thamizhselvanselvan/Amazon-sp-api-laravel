<?php

namespace App\Http\Controllers\shipntrack\Manifest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Inventory\In_Scan;
use App\Models\ShipNTrack\Process\Process_Master;
use App\Models\ShipNTrack\Inventory\Manifest_Item;
use App\Models\ShipNTrack\Inventory\Manifest_Master;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class ExportManifestController extends Controller
{
    public function index(Request $request)
    {
        $data = Manifest_Master::query()->get();
        if ($request->ajax()) {

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex justify-content-center align-items-center"><a href="/shipntrack/export/' . $row->manifest_id . '/details_view" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> View</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipntrack.manifest.export.index');
    }
    public function export_view()
    {
        $destinations  = Process_Master::query()->get();
        return view('shipntrack.manifest.export.export', compact('destinations'));
    }
    public function single_fetch(Request $request)
    {
        $response = $this->fetchdata($request);

        if (count($response) > 0) {
            return response()->json(['success' => 'success', 'data' =>   $response]);
        } else {
            return response()->json(["error" => 'No data Found..Please Check The Manifest ID']);
        }
    }

    public function fetchdata($request)
    {

        $destinations = explode('_', $request->mode);

        if ($request->type == 'single') {
            $data = In_Scan::with('process')->where('awb_number',  $request->awb)->where('export_status', 0)->where('destination', $destinations[0])->get();
        } else if ($request->type == 'bulk') {
            $data = In_Scan::with('process')->where('manifest_id', $request->awb)->where('export_status', 0)->where('destination', $destinations[0])->get();
        }

        return $data;
    }
    public function export_store(Request $request)
    {

        /* Manisfist Id Generate */
        $mode_array = explode('_', $request->mode);
        $val = Manifest_Master::query()
            ->select(('manifest_id'))
            ->orderby('created_at', 'desc')->first();

        if ((!$val)) {
            $ship_id = $mode_array[1] . $mode_array[2] . '100001';
        } else {
            $existing_id = substr($val->manifest_id, 4);

            $new_id = $existing_id + 1;
            $ship_id = $mode_array[1] . $mode_array[2] . $new_id;
        }

        foreach ($request->awb as $key => $awb) {

            $no_of_items[] = ($request->order_id[$key]);
            // $intl_awb = $request->forwarder_1_awb;
        }
        /* Insert Manifest masters */

        $data = [
            'manifest_id' => $ship_id,
            'total_items' => count($no_of_items),
            'awb_number' => $awb,
            'international_awb_number' =>  $request->forwarder_1_awb,

        ];
        Manifest_Master::create($data);

        /* Insert Manifest Items */
        foreach ($request->awb as $key => $awb) {
            $manifest_items = [
                'manifest_id' => $ship_id,
                'awb' =>  $awb,
                'international_awb_number' =>  $request->forwarder_1_awb,
                'inscan_manifest_id' =>  $request->inscan_manefist[$key],
                'order_id' =>  $request->order_id[$key],
                'destination' => $request->destination[$key],
                'purchase_tracking_id' => $request->tracking[$key],
                'forwarder_1' =>  $request->forwarder_1,
                'forwarder_1_awb' => $request->forwarder_1_awb,
            ];
            Manifest_Item::create($manifest_items);
            In_Scan::where('purchase_tracking_id', $request->tracking[$key])->update(['export_status' => 1]);


            /* Updatwe Forwarder */
            $insert_data = [
                'forwarder_1' => $request->forwarder_1,
                'forwarder_1_awb' => $request->forwarder_1_awb,
                'forwarder_1_flag' => 0,
            ];

            if ($request->destination[$key] == 'AE') {
                Trackingae::where('purchase_tracking_id', $request->tracking[$key])->update($insert_data);
            } elseif ($request->destination[$key] == 'IN') {
                Trackingin::where('purchase_tracking_id', $request->tracking[$key])->update($insert_data);
            } elseif ($request->destination[$key] == 'SA') {
                Trackingksa::where('purchase_tracking_id', $request->tracking[$key])->update($insert_data);
            }
        }
        return response()->json(['success' => 'Shipment has Created successfully']);
    }

    public function details_view(Request $request,$manifest_id){
        $data = Manifest_Item::with(['CourierPartner1'])->where(['manifest_id' =>  $manifest_id])->get();
        return view('shipntrack.manifest.export.view',compact('data'));
    }
}
