<?php

namespace App\Http\Controllers\shipntrack\Forwarder;

use League\Csv\Reader;
use AWS\CRT\HTTP\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Packet\PacketForwarder;
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

        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        $forwarder_details = [];

        $awb = [
            'f1_name' => '',
            'ft_1' => '',
            'f2_name' => '',
            'ft_2' => ''
        ];

        foreach ($csv as $key => $value) {

            if ($ft_1 = $value['forwarder_1_awb']) {

                $awb['f1_name'] = $value['forwarder_1'];
                $awb['ft_1'] = $ft_1;
            }
            if ($ft_2 = $value['forwarder_2_awb']) {

                $awb['f2_name'] = $value['forwarder_2'];
                $awb['ft_2'] = $ft_2;
            }

            $forwarder_details[] = $awb;
            $tracking[] =  $value;
        }

        PacketForwarder::upsert($tracking, 'order_id_awb_no_unique', ['order_id', 'awb_no', 'forwarder_1', 'forwarder_1_awb', 'forwarder_2', 'forwarder_2_awb']);

        //
    }
}
