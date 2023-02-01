<?php

namespace App\Http\Controllers\V2\Oms\StatusMaster;

use App\Http\Controllers\Controller;
use App\Models\V2\OMS\StatusMaster;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OmsController extends Controller
{
    //
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = StatusMaster::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex"><a href="/v2/oms/status-master/edit/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<div class="d-flex ml-1"><a href="/v2/oms/status-master/remove/' . $row->id . '" class="remove btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Remove</a>';

                    return $actionBtn;
                })
                ->make(true);
        }
        return view('v2.oms.status_master.index');
    }


    public function AddStatusMaster(Request $request)
    {
        $status_data = $request->validate([
            'code' => 'required|unique:oms.status_masters',
            'status' => 'required',
            'active' => 'required'
        ]);

        StatusMaster::insert($status_data);
        return redirect()->intended('/v2/oms')->with('success', 'Add oms status successfully.');
    }


    public function EditStatusMaster($id)
    {
        $records = StatusMaster::where('id', $id)->get();
        return view('v2.oms.status_master.index', compact('records'));
    }

    public function UpdateStatusMaster(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'status' => 'required',
            'active' => 'required'
        ]);

        $update = StatusMaster::find($id);
        $update->code = $request->code;
        $update->status = $request->status;
        $update->active = $request->active;
        $update->update();
        return redirect()->intended('/v2/oms')->with('success', 'Oms status updated successfully.');
    }

    public function DeleteStatusMaster($id)
    {
        StatusMaster::find($id)->delete();
        return redirect()->intended('/v2/oms')->with('danger', 'Oms status has been deleted successfully.');
    }


    public function RecycleStatusMaster(Request $request)
    {
        $data = StatusMaster::onlyTrashed()->orderBy('id', 'DESC')->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex ml-1"><a href="/v2/oms/status-master/restore/' . $row->id . '" class="restore btn btn-success btn-sm"><i class="far fa-trash-alt"></i> Restore</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.oms.status_master.bin');
    }

    public function RestoreStatusMaster($id)
    {
        StatusMaster::where('id', $id)->restore();
        return redirect()->intended('/v2/oms')->with('success', 'Osm status has been restored successfully.');
    }
}
