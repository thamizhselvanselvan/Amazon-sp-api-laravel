<?php

namespace App\Http\Controllers\shipntrack;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ShipNTrack\Booking;
use App\Http\Controllers\Controller;

class BookingMasterController extends Controller
{
    public function index(Request $request)
    {
        $data =  Booking::query()->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->id == 1) {
                        return '';
                    } else {
                        $actionBtn = '<div class="d-flex"><a href="/shipntrack/booking/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>'; 
                        $actionBtn .= '<div class="d-flex"><a href="/shipntrack/booking/' . $row->id . '/remove" class="delete btn btn-danger btn-sm ml-2 remove"><i class="far fa-trash-alt"></i> Remove</a>';
                        return $actionBtn;
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipntrack.Booking.index');
    }

    public function bookingsave(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Booking::create(['name' => $request->name]);
        return redirect('/shipntrack/booking')->with("success", "Record has been inserted successfully!");
    }

    public function bookingedit($id)
    {
        $record = Booking::find($id)->toArray();
        return view('shipntrack.Booking.index', compact('record'));
    }
    
    public function bookingformedit(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Booking::where('id', $request->update_id)->update(['name' => $request->name]);
        return redirect('/shipntrack/booking')->with("success", "Record has been Updated successfully!");
    }

    public function bookingremove($id)
    {
        booking::find($id)->delete();
        return redirect('/shipntrack/booking')->with("success", "Record has been deleted successfully!");
    }

    
}
