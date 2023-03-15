<?php

namespace App\Http\Controllers\shipntrack\Courier;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Courier\CourierPartner;

class CourierPartnerController extends Controller
{
    // public function index(Request $request)
    // {
    //     if ($request->_token) {

    //         $request->validate([
    //             'c_name' => 'required',
    //             'source' => 'required',
    //             'destination' => 'required',
    //             'code' => 'required'
    //         ]);

    //         $name = $request->c_name;
    //         $source = $request->source;
    //         $destination = $request->destination;
    //         $code = $request->code;

    //         $source_des = $source . '-' . $destination;
    //         CourierPartner::upsert(
    //             [
    //                 'name' => $name,
    //                 'source_destination' => $source_des,
    //                 'courier_code' => $code
    //             ],

    //             'name_source_des_unique',
    //             ['name', 'source_destination', 'courier_code']
    //         );
    //     } elseif ($request->ajax()) {

    //         $CourierPartner = CourierPartner::get();
    //         return DataTables::of($CourierPartner)
    //             ->make(true);
    //     }

    //     return view('shipntrack.Courier_partner.index');
    // }
    public function index(Request $request)
    {
        $data =  CourierPartner::query();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/shipntrack/courier/partner/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipntrack.Courier_partner.index');
    }
    public function create()
    {
        return view('shipntrack.Courier_partner.add');
    }

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required|min:3|max:100',
            'status' => 'required|in:0,1',
            'source' => 'required|in:IN,USA,AE,KSA',
            'destination' => 'required|in:IN,USA,AE,KSA',
            'type' =>   'required|in:Domestic,International',
            'code' =>   'required',

        ]);

        CourierPartner::create([
            'name' => $request->name,
            'source' => $request->source,
            'destination' => $request->destination,
            'active' => $request->status,
            'type' => $request->type,
            'courier_code' => $request->code,
            'key1' => $request->key1,
            'key2' => $request->key2,
            'key3' => $request->key3,
            'key4' => $request->key4,
            'key5' => $request->key5,

        ]);

        return redirect()->route('snt.courier.index')->with('success', 'Courier Partner' . $request->name . ' has been created successfully');
    }

    public function edit($id)
    {
        $data =  CourierPartner::where('id', $id)->get()->first();
        return view('shipntrack.Courier_partner.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|min:3|max:100',
            'status' => 'required|in:0,1',
            'source' => 'required|in:IN,USA,AE,KSA',
            'destination' => 'required|in:IN,USA,AE,KSA',
            'type' =>   'required|in:Domestic,International',
            'code' =>   'required',

        ]);

        CourierPartner::where('id', $id)->update([
            'name' => $request->name,
            'source' => $request->source,
            'destination' => $request->destination,
            'active' => $request->status,
            'type' => $request->type,
            'courier_code' => $request->code,
            'key1' => $request->key1,
            'key2' => $request->key2,
            'key3' => $request->key3,
            'key4' => $request->key4,
            'key5' => $request->key5,

        ]);

        return redirect()->route('snt.courier.index')->with('success', 'Courier Partner has been updated successfully');
    }

    public function destroy($id)
    {
        CourierPartner::where('id', $id)->delete();
        return redirect()->route('snt.courier.index')->with('success', 'Courier Partner has been Deleted successfully');
    }
}
