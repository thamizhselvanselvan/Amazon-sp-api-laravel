<?php

namespace App\Http\Controllers\Shipntrack\ProcessManagement;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ShipNtrack\Process\Process_Master;


class ProcessManagementController extends Controller
{
    public function index(Request $request)
    {
      
        $data =  Process_Master::query()->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ( $userId = Auth::id() == 1) {
                    $actionBtn = '<div class="d-flex"><a href="/shipntrack/process/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<div class="d-flex"><a href="/shipntrack/process/' . $row->id . '/remove" class="delete btn btn-danger btn-sm ml-2 remove"><i class="far fa-trash-alt"></i> Remove</a>';
                    return $actionBtn; 
                    } else {
                    return $actionBtn = '<div class="d-flex"><a href="/shipntrack/process/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                      
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipntrack.Process_master.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'source' => 'required',
            'destination' => 'required',
            'process_id' => 'required',
        ]);
        if ($request->process_id > 99 || $request->process_id < 9) {
            return redirect('/shipntrack/process/home')->with("warning", "Please Enter Process ID Between 10 and 99");
        }
        Process_Master::create([
            'source' => $request->source,
            'destination' => $request->destination,
            'process_id' => $request->process_id
        ]);
        return redirect('/shipntrack/process/home')->with("success", "Record has been inserted successfully!");
    }
    public function update_view(Request $request, $id)
    {
        $record = Process_Master::find($id)->toArray();

        return view('shipntrack.Process_master.index', compact('record'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'source' => 'required',
            'destination' => 'required',
            'process_id' => 'required',
        ]);
        if ($request->process_id > 99 || $request->process_id < 9) {
            return redirect('/shipntrack/process/home')->with("warning", "Please Enter Process ID Between 10 and 99");
        }
        Process_Master::where('id', $request->update_id)->update([
            'source' => $request->source,
            'destination' => $request->destination,
            'process_id' => $request->process_id
        ]);
        return redirect('/shipntrack/process/home')->with("success", "Record has been Updated successfully!");
    }
    public function remove($id)
    {
        Process_Master::find($id)->delete();
        return redirect('/shipntrack/process/home')->with("success", "Record has been deleted successfully!");
    }
}
