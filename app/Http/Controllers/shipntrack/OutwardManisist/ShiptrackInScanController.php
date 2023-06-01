<?php

namespace App\Http\Controllers\shipntrack\OutwardManisist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Courier\CourierPartner;

class ShiptrackInScanController extends Controller
{
    public function  index()
    {

        return view('shipntrack.manifist.inscan.index');
    }
    public function  view()
    {
        $destinations = CourierPartner::select('source', 'destination')
            ->where('type', 1)
            ->groupBy('source', 'destination')
            ->get()
            ->toArray();
        return view('shipntrack.manifist.inscan.add', compact('destinations'));
    }
    public function get_details(Request $request)
    {
       
    }
}
