<?php

namespace App\Http\Controllers\shipntrack\Courier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Courier\CourierPartner;

class CourierPartnerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->_token) {

            $request->validate([
                'c_name' => 'required',
                'source' => 'required',
                'destination' => 'required',
                'code' => 'required'
            ]);

            $name = $request->c_name;
            $source = $request->source;
            $destination = $request->destination;
            $code = $request->code;

            $source_des = $source . '-' . $destination;
            CourierPartner::upsert(
                [
                    'name' => $name,
                    'source_destination' => $source_des,
                    'courier_code' => $code
                ],

                'name_source_des_unique',
                ['name', 'source_destination', 'courier_code']
            );
        } elseif ($request->ajax()) {

            $CourierPartner = CourierPartner::get();
            return DataTables::of($CourierPartner)
                ->make(true);
        }

        return view('shipntrack.Courier_partner.index');
    }
}
