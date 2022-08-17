<?php

namespace App\Http\Controllers\shipntrack\Forwarder;

use League\Csv\Reader;
use League\Csv\Writer;
use AWS\CRT\HTTP\Response;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Eval_;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\ShipNTrack\Packet\PacketForwarder;

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
                if (strtoupper($courier) == 'SMSA') {

                    $smas_key = $key . '_awb';
                    $awb['Smsa'] = $value[$smas_key];
                } elseif (strtoupper($courier) == 'BOMBINO') {

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

                if ($key == 'Smsa') {

                    $class = 'ShipNTrack\\SMSA\\SmsaGetTracking';
                    $queue_type = 'tracking';
                    $awbNo_array = [$awb_no];
                    jobDispatchFunc(class: $class, parameters: $awbNo_array, queue_type: $queue_type);
                } elseif ($key == 'Bombino') {

                    $class = "ShipNTrack\\Bombino\\BombinoGetTracking";
                    $parameters['awb_no'] = $awb_no;
                    $queue_type = 'tracking';
                    jobDispatchFunc(class: $class, parameters: $parameters, queue_type: $queue_type);
                }
            }
        }
        return redirect()->intended('/shipntrack/forwarder/upload')->with("success", "Tracking Details Uploaded");
    }

    public function missingexpview()
    {
        return view('shipntrack.Forwarder.export');
    }

    public function missexport(Request $request)
    {
        if ($request->ajax()) {
            $records = [];

            $filter  = explode('!=', $request->selected);

            $date = $filter[0];
            $first_forwarder = $filter[1];
            $second_forwarder = $filter[2];

            $first_forwarder  = $first_forwarder == 'false' ? NULL : $first_forwarder;
            $second_forwarder  = $second_forwarder == 'false' ? NULL : $second_forwarder;

            $dbheaders = ['order_id', 'awb_no', 'forwarder_1', 'forwarder_1_awb', 'forwarder_2', 'forwarder_2_awb'];

            $records = PacketForwarder::select($dbheaders)
                ->when(!empty(trim($date)), function ($query) use ($date) {
                    $date = $this->split_date($date);
                    $query->whereBetween('created_at', [$date[0], $date[1]]);
                })
                ->when(!is_null($first_forwarder), function ($query) {

                    $query->where('forwarder_1', '');
                })
                ->when(!is_null($second_forwarder), function ($query) {

                    $query->where('forwarder_2', '');
                })
                ->get();

            $headers = [

                'order ID',
                'AWB no',
                'forwarder 1',
                'forwarder_1_awb',
                'forwarder_2',
                'forwarder_2_awb'

            ];
            $exportFilePath = 'farwarder\missing.csv';
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);

            $writer->insertAll($records->toArray());
        }
    }
    public function downexp()
    {
        return Storage::download('farwarder\missing.csv');
    }

    public function split_date($date)
    {
        $date = explode(' - ', $date);
        return [trim($date[0]), trim($date[1])];
    }
}
