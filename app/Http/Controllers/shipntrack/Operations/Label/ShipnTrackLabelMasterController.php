<?php

namespace App\Http\Controllers\shipntrack\Operations\Label;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\ShipNTrack\Operation\LabelMaster\LabelMaster;

class ShipnTrackLabelMasterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $label_masters = LabelMaster::query()
                ->orderBy('id', 'DESC')
                ->get()
                ->toArray();

            return DataTables::of($label_masters)
                ->editColumn('logo', function ($label_masters) {
                    $img = $label_masters['file_path'];
                    return "<img src='/image/$img' class='img-fluid' alt=$img style='height:30px; width:30px'>";
                })
                ->rawColumns(['logo'])
                ->make(true);
        }
        return view('shipntrack.Operation.LabelManagement.Master.index');
    }

    public function LabelMasterFormSubmit(Request $request)
    {
        $file_name = $request->file('logo')->getClientOriginalName();
        $master_records = [
            'source'            => $request->source,
            'destination'       => $request->destination,
            'file_path'              => $file_name,
            'return_address'    => $request->return_address
        ];

        file_put_contents("image/$file_name", file_get_contents($request->logo));
        LabelMaster::create($master_records, ['source_destination_unique'], [
            'source',
            'destination',
            'file_path',
            'return_address'
        ]);
        return redirect('shipntrack/label/master')->with('success', 'Record has been inserted successfully!');
    }
}
