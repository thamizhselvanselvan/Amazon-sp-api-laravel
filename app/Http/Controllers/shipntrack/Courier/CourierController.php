<?php

namespace App\Http\Controllers\shipntrack\Courier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Courier\Courier;

class CourierController extends Controller
{

    public function index(Request $request)
    {
        $data =  Courier::query()->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/shipntrack/courier/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<div class="d-flex"><a href="/shipntrack/courier/' . $row->id . '/remove" class="delete btn btn-danger btn-sm ml-2 remove"><i class="far fa-trash-alt"></i> Remove</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipntrack.courier.index');
    }

    public function couriergsave(Request $request)
    {
        $request->validate([
            'c_name' => 'required',
            'code' => 'required',
        ]);
        Courier::create(['courier_name' => $request->c_name, 'courier_code' => $request->code]);
        return redirect('/shipntrack/courier')->with("success", "Record has been inserted successfully!");
    }

    public function courieredit($id)
    {
        $record = Courier::find($id)->toArray();
        return view('shipntrack.courier.index', compact('record'));
    }

    public function courierupdate(Request $request)
    {
        $request->validate([
            'c_name' => 'required',
            'code' => 'required',
        ]);
        Courier::where('id', $request->update_id)->update(['courier_name' => $request->c_name, 'courier_code' => $request->code]);
        return redirect('/shipntrack/courier')->with("success", "Record has been Updated successfully!");
    }

    public function courierremove($id)
    {
        Courier::find($id)->delete();
        return redirect('/shipntrack/courier')->with("success", "Record has been deleted successfully!");
    }
}
