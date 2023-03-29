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
        $data =  StatusManagement::query()->with(['courierstatus'])->get();
        if ($request->ajax()) {
            $store_order_item[] = 0;
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('courier_id', function ($row) {
                    $name = $row->courierstatus->courier_name;
                    return $name;
                })
                ->addColumn('booking_master_id', function ($row) {
                    $datas = Booking::select('id', 'name')->get();
                    $selected_status =  StatusManagement::where('id', $row->id)->select('booking_master_id')->get();

                    $status = ($selected_status[0]->booking_master_id);

                    $html = '<select class="w-75" id="select_status" href="javascript:void(0)" name"select">Select Status';
                    $html .= "<option value=null-$row->id>Select Status</option>";
                    foreach ($datas as $data) {
                        if($status == $data->id )
                        {
                            $html .=  "<option value='$data->id-$row->id' selected>$data->name</option>";
                        } else {
                            $html .=  "<option value='$data->id-$row->id'>$data->name</option>";
                        }
                    }
                    return $html;
                })
                ->addColumn('action', function ($row) use ($store_order_item) {
                    if (array_key_exists($row['stop_tracking'], $store_order_item)) {
                        $actionBtn = "<input type='checkbox'  href='javascript:void(0)'  id='stat_stop' name='stats_store' value='$row->id'>";
                    } else {
                        $actionBtn = "<input type='checkbox' checked href='javascript:void(0)'  id='stat_stop' name='stats_store' value='$row->id'>";
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'booking_master_id'])
                ->make(true);
        }
        return view('shipntrack.StatusManagemrnt.index');
    }

    public function storestatus(Request $request)
    {
        //stop Tracking
        StatusManagement::query()->update(['stop_tracking' => '0']);
        $receved_stop = $request->stop_enable;
        $datas =   explode('-', $receved_stop);
        foreach ($datas as $key => $data) {

            StatusManagement::where('id', $data)->update(['stop_tracking' => '1']);
        }

        //update Status
        $receved_status = $request->status;
        $status_datas =   explode('|', $receved_status);
        foreach ($status_datas as $key => $status) {
            if ($key == 0) {
                continue;
            } else {

                $status_val =   explode('-', $status);

                if (isset($status_val['0']) && isset($status_val['1'])) {

                    $stat = ($status_val['0']);
                    if ($stat == 'null') {
                        $stat = null;
                    }

                    $id = ($status_val['1']);
                    StatusManagement::where('id', $id)->update(['booking_master_id' => $stat]);
                } else {
                    return response()->json('error');
                }
            }
        }
        return response()->json('success');
    }
}
