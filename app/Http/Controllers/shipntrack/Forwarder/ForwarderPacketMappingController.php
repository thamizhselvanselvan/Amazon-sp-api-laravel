<?php

namespace App\Http\Controllers\shipntrack\Forwarder;

use AWS\CRT\HTTP\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ForwarderPacketMappingController extends Controller
{

    public function index()
    {
        return view('shipntrack.Forwarder.index');
    }

    public function Upload()
    {
        return view('shipntrack.Forwarder.upload');
    }

    public function templateDownload()
    {
        $file_path = public_path('template/Forwarder-Tracking-Template.csv');
        return response()->download($file_path);
    }

    public function save(Request $request)
    {
        $request->validate([
            'forwarder_awb' => 'required|mimes:csv,txt,xls,xlsx'
        ]);

        $path = 'ShipnTrack/Forwarder/Tracking_No.csv';

        $source = file_get_contents($request->forwarder_awb);

        Storage::put($path, $source);

        //
    }
}
