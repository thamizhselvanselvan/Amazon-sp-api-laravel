<?php

namespace App\Http\Controllers\shipntrack\Courier;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Courier\Courier;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Courier\CourierPartner;

use function Clue\StreamFilter\fun;

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

        $data =  CourierPartner::with(['courier_names'])->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    if ($row->type == 1) {
                        return 'International';
                    } elseif ($row->type == 2) {
                        return 'Domestic';
                    } elseif ($row->type == 3) {
                        return 'International/Domestic';
                    }
                    return $row->type;
                })
                ->editColumn('courier_name', function ($data) {
                    return $data->courier_names->courier_name;
                })

                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/shipntrack/partner/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
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
        $couriers = Courier::query()->get();
        return view('shipntrack.Courier_partner.add', compact('couriers'));
    }

    public function store(Request $request)
    {

        $request->validate([

            'user_name' => 'required',
            'courier_name' => 'required',
            'status' => 'required|in:0,1',
            'source' => 'required|in:IN,USA,AE,KSA',
            'destination' => 'required|in:IN,USA,AE,KSA',
            'type' =>   'required|in:2,1,3',
            'time_zone' =>   'required',

        ]);
        // $code = strtolower($request->code);
        CourierPartner::create([
            'user_name' => $request->user_name,
            'courier_id' => $request->courier_name,
            'source' => $request->source,
            'destination' => $request->destination,
            'active' => $request->status,
            'type' => $request->type,
            'time_zone' => $request->time_zone,
            'user_id' => $request->user_id,
            'password' => $request->password,
            'account_id' => $request->account_id,
            'key1' => $request->key1,
            'key2' => $request->key2,

        ]);

        return redirect()->route('snt.courier.index')->with('success', 'Courier Partner' . $request->name . ' has been created successfully');
    }

    public function edit($id)
    {
        $data =  CourierPartner::where('id', $id)->get()->first();
        $couriers = Courier::query()->get();
        return view('shipntrack.Courier_partner.edit', compact('data', 'couriers'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'user_name' => 'required',
            'courier_name' => 'required',
            'status' => 'required|in:0,1',
            'source' => 'required|in:IN,USA,AE,KSA',
            'destination' => 'required|in:IN,USA,AE,KSA',
            'type' =>   'required|in:3,1,2',
            'time_zone' =>   'required',

        ]);
        $code = strtolower($request->code);
        CourierPartner::where('id', $id)->update([
            'user_name' => $request->user_name,
            'courier_id' => $request->courier_name,
            'source' => $request->source,
            'destination' => $request->destination,
            'active' => $request->status,
            'type' => $request->type,
            'time_zone' => $request->time_zone,
            'user_id' => $request->user_id,
            'password' => $request->password,
            'account_id' => $request->account_id,
            'key1' => $request->key1,
            'key2' => $request->key2,

        ]);

        return redirect()->route('snt.courier.index')->with('success', 'Courier Partner has been updated successfully');
    }

    public function destroy($id)
    {
        CourierPartner::where('id', $id)->delete();
        return redirect()->route('snt.courier.index')->with('success', 'Courier Partner has been Deleted successfully');
    }
}
