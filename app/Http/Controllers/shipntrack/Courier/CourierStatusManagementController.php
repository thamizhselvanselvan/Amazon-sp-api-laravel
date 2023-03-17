<?php

namespace App\Http\Controllers\shipntrack\Courier;

use Illuminate\Http\Request;
use App\Models\ShipNTrack\Booking;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Courier\StatusManagement;

class CourierStatusManagementController extends Controller
{
    public function index(Request $request)
    {
        $data =  StatusManagement::query()->with(['courierpartner'])->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('courier_partner_id', function ($row) {
                    $name = $row->courierpartner->name;
                    return $name;
                })
                ->addColumn('booking_master_id', function ($row) {
                    $datas = Booking::select('id', 'name')->get();

                    $html = '<select class="w-50" id="select" href="javascript:void(0)" name"select">Select Status';
                    $html .= "<option value='->id'>Select Status</option>";
                    foreach ($datas as $data) {
                        $html .=  "<option value='$data->id'>$data->name</option>";
                    }
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = "<input type='checkbox' href='javascript:void(0)'  id='stat_stop' name='stats_store' value='$row->id'>";
                    return $actionBtn;
                })
                ->rawColumns(['action', 'booking_master_id'])
                ->make(true);
        }
        return view('shipntrack.StatusManagemrnt.index');
    }

    public function storestatus(Request $request)
    {
        $receved_array = $request->status;

        foreach ($receved_array as $key => $data) {
            StatusManagement::where('id', $key)->update(['booking_master_id' => $data, 'stop_tracking' => '1']);
        }
        return response()->json('success');
    }
}
