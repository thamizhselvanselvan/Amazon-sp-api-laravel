<?php

namespace App\Http\Controllers\shipntrack\Operations\Label;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Operation\LabelMaster\LabelMaster;

class ShipnTrackLabelMasterController extends Controller
{
    public function index(Request $request)
    {
        return view('shipntrack.Operation.LabelManagement.Master.index');
    }

    public function LabelMasterFormSubmit(Request $request)
    {
        $master_records = [
            'source'            => $request->source,
            'destination'       => $request->destination,
            'file_path'              => $request->file('logo')->getClientOriginalName(),
            'return_address'    => $request->return_address
        ];
        $testing = LabelMaster::upsert($master_records, ['source_destination_unique'], [
            'source',
            'destination',
            'file_path',
            'return_address'
        ]);
        return redirect('shipntrack/label/master')->with('success', 'Record has been inserted successfully!');
    }
}
