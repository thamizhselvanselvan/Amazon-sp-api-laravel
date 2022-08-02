<?php

namespace App\Http\Controllers\shipntrack\Bombino;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\shipntrack\SMSA\SmsaExperessController;
use App\Models\ShipNTrack\Bombino\BombinoTracking;
use App\Models\ShipNTrack\Bombino\BombinoTrackingDetails;
use Illuminate\Support\Facades\Http;

class BombinoExpressController extends Controller
{
    public function index()
    {
        return view('shipntrack.Bombino.index');
    }

    public function upload()
    {
        return view('shipntrack.Bombino.upload');
    }

    public function getTracking(Request $request)
    {
        $request->validate([
            'bombino_awbNo' => 'required|min:10',
        ]);

        $tracking_id = $request->bombino_awbNo;
        $datas = preg_split('/[\r\n| |:|,]/', $tracking_id, -1, PREG_SPLIT_NO_EMPTY);
        $datas = array_unique($datas);

        foreach ($datas as $awb_no) {

            $class = 'ShipNTrack\\Bombino\\BombinoGetTracking';
            $parameters['awb_no'] = $awb_no;
            $queue_type = 'tracking';
            jobDispatchFunc(class: $class, parameters: $parameters, queue_type: $queue_type);
        }

        return redirect('/shipntrack/bombino')->with('success', 'Fetching Tracking Details');
    }
}
