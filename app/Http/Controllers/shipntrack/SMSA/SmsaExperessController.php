<?php

namespace App\Http\Controllers\shipntrack\SMSA;

use Carbon\Carbon;
use Illuminate\Http\Request;
use function Clue\StreamFilter\fun;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Aramex\AramexTracking;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Contracts\DataTable;

use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;
use App\Models\ShipNTrack\ForwarderMaping\IntoKSA;

class SmsaExperessController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = [];
            if ($request->sourceDestination == 'ind_to_uae') {

                $data = IntoAE::select('awb_number', 'forwarder_1_awb', 'forwarder_2_awb', 'forwarder_3_awb', 'forwarder_4_awb', 'created_at')
                    ->orderBy('awb_number', 'DESC')
                    ->get()
                    ->toArray();
            } elseif ($request->sourceDestination == 'ind_to_ksa') {
                $data = IntoKSA::select('awb_number', 'forwarder_1_awb', 'forwarder_2_awb', 'forwarder_3_awb', 'forwarder_4_awb', 'created_at')
                    ->orderBy('awb_number', 'DESC')
                    ->get()
                    ->toArray();
            }
            return DataTables::of($data)
                ->editColumn('forwarder1_awb', function ($data) {
                    $forwarder1 = $data['forwarder_1_awb'] ?? 'NA';
                    return $forwarder1;
                })
                ->editColumn('forwarder2_awb', function ($data) {
                    $forwarder2 = $data['forwarder_2_awb'] ?? 'NA';
                    return $forwarder2;
                })
                ->editColumn('forwarder3_awb', function ($data) {
                    $forwarder2 = $data['forwarder_3_awb'] ?? 'NA';
                    return $forwarder2;
                })
                ->editColumn('forwarder4_awb', function ($data) {
                    $forwarder2 = $data['forwarder_4_awb'] ?? 'NA';
                    return $forwarder2;
                })
                ->editColumn('created_date', function ($data) {
                    $created_date = Carbon::parse($data['created_at']);
                    return $created_date;
                })
                ->addColumn('action', function ($data) use ($request) {
                    $action = "<a href='/shipntrack/courier/moredetails/" . $request->sourceDestination . "/" . $data['awb_number'] . "' class='' target='_blank'>More Details</a>";
                    return $action;
                })
                ->rawColumns(['forwarder1_awb', 'forwarder2_awb', 'forwarder3_awb', 'forwarder4_awb', 'created_date', 'action'])
                ->make(true);
        }
        return view('shipntrack.Smsa.index');
    }

    public function PacketMoreDetails($sourceDestination, $awbNo)
    {
        $colunmName = ["ss_ae" => "date", "am_ae" => "update_date_time", "ss_ksa" => "date", "am_ksa" => "update_date_time"];
        $courierFilePath =  ["ss_ae" => "SMSA", "am_ae" => "Aramex", "bom" => "Bombino", "ss_ksa" => "SMSA", "am_ksa" => "Aramex"];
        $courierModelName =  ["ss_ae" => "SmsaTrackings", "am_ae" => "AramexTrackings", "bom" => "BombinoTrackings", "ss_ksa" => "SmsaTrackings", "am_ksa" => "AramexTrackings"];
        $courierTableName =  ["ss_ae" => "ae_smsa_trackings", "am_ae" => "ae_aramex_trackings", "bom" => "bombino_trackings", "ss_ksa" => "ksa_smsa_trackings", "am_ksa" => "ksa_aramex_trackings"];
        $selectColumns = [
            'ss_ae' => [
                'awbno',
                'date',
                'activity',
                'details',
                'location',
            ],
            'am_ae' => [
                'awbno',
                'update_code',
                'update_description',
                'update_date_time',
                'update_location',
                'comment',
                'gross_weight',
                'chargeable_weight',
                'weight_unit',
            ],
            'bom' > [],
            'ss_ksa' => [
                'awbno',
                'date',
                'activity',
                'details',
                'location',
            ],
            'am_ksa' => [
                'awbno',
                'update_code',
                'update_description',
                'update_date_time',
                'update_location',
                'comment',
                'gross_weight',
                'chargeable_weight',
                'weight_unit',
            ],
        ];
        $result = [];
        if ($sourceDestination == 'ind_to_uae') {

            $result = IntoAE::with(['courierPartner1', 'courierPartner2', 'courierPartner3', 'courierPartner4'])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        } elseif ($sourceDestination == 'ind_to_ksa') {

            $result = IntoKSA::with(['courierPartner1', 'courierPartner2', 'courierPartner3', 'courierPartner4'])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        }

        $records1 = [];
        if (isset($result[0]['forwarder_1_awb'])) {

            $forwarder1_awb = $result[0]['forwarder_1_awb'];
            $forwarder1_courierCode = $result[0]['courier_partner1']['courier_code'];

            $modelName = $courierModelName[$forwarder1_courierCode];
            $path = $courierFilePath[$forwarder1_courierCode];
            $tableName = $courierTableName[$forwarder1_courierCode];

            $table = table_model_change(model_path: $path, model_name: $modelName, table_name: $tableName);
            $records1 = $table->select($selectColumns[$forwarder1_courierCode])
                ->where('awbno', $forwarder1_awb)
                ->orderBy($colunmName[$forwarder1_courierCode], 'DESC')
                ->get()
                ->toArray();
        }
        $records2 = [];
        if (isset($result[0]['forwarder_2_awb'])) {

            $forwarder2_awb = $result[0]['forwarder_2_awb'];
            $forwarder2_courierCode = $result[0]['courier_partner2']['courier_code'];

            $path = $courierFilePath[$forwarder2_courierCode];
            $modelName = $courierModelName[$forwarder2_courierCode];
            $tableName = $courierTableName[$forwarder2_courierCode];

            $table = table_model_change(model_path: $path, model_name: $modelName, table_name: $tableName);
            $records2 = $table->select($selectColumns[$forwarder2_courierCode])
                ->where('awbno', $forwarder2_awb)
                ->orderBy($colunmName[$forwarder2_courierCode], 'DESC')
                ->get()
                ->toArray();
        }
        $records3 = [];
        if (isset($result[0]['forwarder_3_awb'])) {

            $forwarder3_awb = $result[0]['forwarder_3_awb'];
            $forwarder3_courierCode = $result[0]['courier_partner3']['courier_code'];

            $path = $courierFilePath[$forwarder3_courierCode];
            $modelName = $courierModelName[$forwarder3_courierCode];
            $tableName = $courierTableName[$forwarder3_courierCode];

            $table = table_model_change(model_path: $path, model_name: $modelName, table_name: $tableName);
            $records3 = $table->select($selectColumns[$forwarder3_courierCode])
                ->where('awbno', $forwarder3_awb)
                ->orderBy($colunmName[$forwarder3_courierCode], 'DESC')
                ->get()
                ->toArray();
        }
        $records4 = [];
        if (isset($result[0]['forwarder_4_awb'])) {

            $forwarder4_awb = $result[0]['forwarder_4_awb'];
            $forwarder4_courierCode = $result[0]['courier_partner4']['courier_code'];

            $path = $courierFilePath[$forwarder4_courierCode];
            $modelName = $courierModelName[$forwarder4_courierCode];
            $tableName = $courierTableName[$forwarder4_courierCode];

            $table = table_model_change(model_path: $path, model_name: $modelName, table_name: $tableName);
            $records4 = $table->select($selectColumns[$forwarder4_courierCode])
                ->where('awbno', $forwarder2_awb)
                ->orderBy($colunmName[$forwarder4_courierCode], 'DESC')
                ->get()
                ->toArray();
        }

        $data1 = [];
        $data2 = [];
        $data1 = count($records1) > count($records2) ? $records1 : $records2;
        $data2 = count($records1) < count($records2) ? $records1 : $records2;

        if (count($records1) == count($records2)) {
            $data1 = $records1;
            $data2 = $records2;
        }

        return view('shipntrack.Smsa.packetDetails', compact('data1', 'data2', 'records3', 'records4'));
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
