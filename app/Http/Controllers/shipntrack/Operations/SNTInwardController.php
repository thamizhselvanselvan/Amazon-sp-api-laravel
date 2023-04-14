<?php

namespace App\Http\Controllers\ShipNTrack\Operations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Courier\CourierPartner;

class SNTInwardController extends Controller
{
    public function index(Request $request)
    {
        return view('shipntrack.Operation.inward.index');
    } 
    public function inward_view()
    {
        $modes = ['IND2UAE','USA2UAE','USA2IND'];
        // po($mode);
        // $destinations = CourierPartner::select('source', 'destination')
        //     ->groupBy('source', 'destination')
        //     ->get()
        //     ->toArray();
            
        return view('shipntrack.Operation.inward.inward',compact('modes'));
    }
}
