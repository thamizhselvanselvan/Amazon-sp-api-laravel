<?php

namespace App\Http\Controllers\shipntrack\SMSA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;

use function Clue\StreamFilter\fun;

class SmsaExperessController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SmsaTrackings::orderBy('date', 'desc')->get()->unique('awbno');
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $action = "<a href='/shipntrack/smsa/moredetails/" . $data['awbno'] . "' class='' target='_blank'>More Details</a>";
                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipntrack.Smsa.index');
    }

    public function PacketMoreDetails($awbNo)
    {
        $result = SmsaTrackings::where('awbno', $awbNo)->orderBy('date', 'desc')->get();
        //    dd($result);
        return view('shipntrack.Smsa.packetDetails', compact('result'));
    }
    public function uploadAwb()
    {
        return view('shipntrack.Smsa.upload');
    }

    public function GetTrackingDetails(Request $request)
    {
        $request->validate([
            'smsa_awbNo' => 'required|min:10',
        ]);

        $tracking_id = $request->smsa_awbNo;

        $datas = preg_split('/[\r\n| |:|,]/', $tracking_id, -1, PREG_SPLIT_NO_EMPTY);
        $datas = array_unique($datas);

        $count = 0;
        $awbNo_array = [];

        $class = 'ShipNTrack\\SMSA\\SmsaGetTracking';
        $queue_type = 'tracking';

        foreach ($datas as $awbNo) {
            if ($count == 5) {

                jobDispatchFunc(class: $class, parameters: $awbNo_array, queue_type: $queue_type);
                $awbNo_array = [];
                $count = 0;
            }
            $awbNo_array[] = $awbNo;
            $count++;
        }

        jobDispatchFunc(class: $class, parameters: $awbNo_array, queue_type: $queue_type);
        return redirect()->intended('/shipntrack/smsa')->with('success', 'Tracking Details Saved');
    }
}
