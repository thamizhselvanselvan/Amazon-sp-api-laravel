<?php

namespace App\Http\Controllers\shipntrack\Operations\Label;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\ShipNTrack\Operation\LabelMaster\LabelMaster;
use RedBeanPHP\LabelMaker;

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
                    if (!empty($img)) {

                        return "<img src='/image/$img' class='img-fluid' alt=$img style='height:30px; width:30px'>";
                    }
                })
                ->addColumn('action', function ($label_masters) {

                    $id = $label_masters['id'];
                    $source = $label_masters['source'];
                    $destination = $label_masters['destination'];
                    $return_address = $label_masters['return_address'];

                    return "<a id='edit_form' data-toggle='modal' data-id='$id' data-source='$source' data-destination='$destination' data-address='$return_address' href='javascript:void(0)' class='edit btn btn-primary btn-sm'>
                    <i class='fas fa-edit'></i> Edit </a>";
                })
                ->rawColumns(['logo', 'action'])
                ->make(true);
        }
        return view('shipntrack.Operation.LabelManagement.Master.index');
    }

    public function LabelMasterFormSubmit(Request $request)
    {

        $file_name = $request->logo != '' ? $request->file('logo')->getClientOriginalName() : NULL;
        $master_records = [
            'source'            => $request->source,
            'destination'       => $request->destination,
            'file_path'         => $file_name,
            'return_address'    => $request->return_address
        ];
        if ($request->logo != '') {

            file_put_contents("image/$file_name", file_get_contents($request->logo));
        }
        LabelMaster::upsert($master_records, ['source_destination_unique'], [
            'source',
            'destination',
            'file_path',
            'return_address'
        ]);
        return redirect('shipntrack/label/master')->with('success', 'Record has been inserted successfully!');
    }

    public function LabelMasterFormEdit(Request $request)
    {

        $file_name = $request->logo != '' ? $request->file('logo')->getClientOriginalName() : NULL;
        $master_records = [
            'source'            => $request->source,
            'destination'       => $request->destination,
            'file_path'         => $file_name,
            'return_address'    => $request->return_address
        ];
        if ($request->logo != '') {

            file_put_contents("image/$file_name", file_get_contents($request->logo));
        }

        LabelMaster::where('id', $request->id)->update($master_records);
        return redirect('shipntrack/label/master')->with('success', 'Record has been inserted successfully!');
    }
}
