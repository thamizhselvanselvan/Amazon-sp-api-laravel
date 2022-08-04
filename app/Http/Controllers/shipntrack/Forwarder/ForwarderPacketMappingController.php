<?php

namespace App\Http\Controllers\shipntrack\Forwarder;

use League\Csv\Reader;
use AWS\CRT\HTTP\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Eval_;

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
            'Smsa' => '',
            'Bombino' => '',
        ];

        foreach ($csv as $key => $value) {
            foreach ($value as $key => $courier) {
                if (str_contains('SMSA', strtoupper($courier))) {
                    $smas_key = $key . '_awb';
                    $awb['Smsa'] = $value[$smas_key];
                } elseif (str_contains('BOMBINO', strtoupper($courier))) {
                    $bombino_key = $key . '_awb';
                    $awb['Bombino'] = $value[$bombino_key];
                }
            }
            $forwarder_details[] = $awb;
            $tracking[] =  $value;
        }
        PacketForwarder::upsert($tracking, 'order_id_awb_no_unique', ['order_id', 'awb_no', 'forwarder_1', 'forwarder_1_awb', 'forwarder_2', 'forwarder_2_awb']);

        foreach ($forwarder_details as $value) {
            foreach ($value as $key => $awb_no) {

                echo $key . '=>' . $awb;
                echo "<hr>";

                $folder_name = $key == 'Smsa' ? 'SMSA' : $key;
            }
        }

        $class = 'ShipNTrack\\Bombino\\BombinoGetTracking';
        $parameters['awb_no'] = $awb_no;
        $queue_type = 'tracking';
        jobDispatchFunc(class: $class, parameters: $parameters, queue_type: $queue_type);

        $class = 'ShipNTrack\\SMSA\\SmsaGetTracking';
        $queue_type = 'tracking';
        $awbNo_array = [];
        jobDispatchFunc(class: $class, parameters: $awbNo_array, queue_type: $queue_type);
    }
}
