<?php

namespace App\Http\Controllers\shipntrack\Tracking;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\V2\OMS\StatusMaster;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Courier\CourierPartner;
use App\Models\ShipNTrack\Courier\StatusManagement;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class CourierTrackingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = [];
            if ($request->sourceDestination == 'AE') {

                $data = Trackingae::select('awb_number', 'forwarder_1_awb', 'forwarder_2_awb', 'forwarder_3_awb', 'forwarder_4_awb', 'created_at')
                    ->orderBy('awb_number', 'DESC')
                    ->get()
                    ->toArray();
            } elseif ($request->sourceDestination == 'IN') {

                $data = Trackingin::select('awb_number', 'forwarder_1_awb', 'forwarder_2_awb', 'forwarder_3_awb', 'forwarder_4_awb', 'created_at')
                    ->orderBy('awb_number', 'DESC')
                    ->get()
                    ->toArray();
            } elseif ($request->sourceDestination == 'KSA') {

                $data = Trackingksa::select('awb_number', 'forwarder_1_awb', 'forwarder_2_awb', 'forwarder_3_awb', 'forwarder_4_awb', 'created_at')
                    ->orderBy('awb_number', 'DESC')
                    ->get()
                    ->toArray();
            }
            return DataTables::of($data)
                ->editColumn('awb_number', function ($data) use ($request) {
                    $awb_number = $request->sourceDestination . $data['awb_number'];
                    return $awb_number;
                })
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
                ->rawColumns(['awb_number', 'forwarder1_awb', 'forwarder2_awb', 'forwarder3_awb', 'forwarder4_awb', 'created_date', 'action'])
                ->make(true);
        }
        return view('shipntrack.Smsa.index');
    }

    public function PacketMoreDetails($sourceDestination, $awbNo)
    {
        $OrderByColunm1 = [
            'SMSA' => 'date',
            'Aramex' => 'update_date_time',
            'Bombino' => 'action_date'

        ];

        $OrderByColunm2 = [
            'SMSA' => 'date',
            'Aramex' => 'update_date_time',
            'Bombino' => 'action_time'

        ];

        $selectColumns = [
            'SMSA' => [
                'date',
                'activity',
                'location',
            ],
            'Aramex' => [
                'update_date_time',
                'update_description',
                'update_location',
            ],
            'Bombino' => [
                'action_date',
                'action_time',
                'event_detail',
                'location'
            ],

        ];

        $result = [];
        if ($sourceDestination == 'AE') {

            $result = Trackingae::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        } elseif ($sourceDestination == 'IN') {

            $result = Trackingin::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        } elseif ($sourceDestination == 'KSA') {

            $result = Trackingksa::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        }

        $forwarder_details = [
            'consignor' => $result[0]['consignor'],
            'consignee' => $result[0]['consignee'],
            'origin' => $result[0]['courier_partner1']['source'],
            'destination' => $result[0]['courier_partner1']['destination'],
        ];

        $forwarder1_record = [];
        if ($result[0]['forwarder_1_awb'] != '' && $result[0]['forwarder_1_flag'] == 0) {

            $awb_no = $result[0]['forwarder_1_awb'];
            $courier_name = $result[0]['courier_partner1']['courier_names']['courier_name'];
            $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

            $forwarder1_data = $table->select($selectColumns[$courier_name])
                ->where('awbno', $awb_no)
                ->orderBy($OrderByColunm1[$courier_name], 'DESC')
                ->orderBy($OrderByColunm2[$courier_name], 'DESC')
                ->get()
                ->toArray();

            $columnName = $result[0]['forwarder_2_awb'] == '' ? 'last_mile_status' : 'first_mile_status';
            $courierActivities = StatusManagement::select('courier_status')
                ->join('courier', 'courier.id', '=', 'status_master.courier_id')
                ->where($columnName, 1)
                ->where('courier_name', $courier_name)
                ->get()
                ->toArray();

            $courierStatus = [];
            foreach ($courierActivities as $courierActivity) {
                $courierStatus[] = $courierActivity['courier_status'];
            }

            foreach ($forwarder1_data as $data) {
                foreach ($data as $key => $res) {
                    if (in_array(strtoupper($res), $courierStatus)) {

                        $forwarder1_record[] = ['courier_name' => $courier_name, ...$data];
                    }
                }
            }
        }

        $forwarder2_record = [];
        if ($result[0]['forwarder_2_awb'] != '' && $result[0]['forwarder_2_flag'] == 0) {

            $awb_no = $result[0]['forwarder_2_awb'];
            $courier_name = $result[0]['courier_partner2']['courier_names']['courier_name'];
            $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

            $forwarder2_data = $table->select($selectColumns[$courier_name])
                ->where('awbno', $awb_no)
                ->orderBy($OrderByColunm1[$courier_name], 'DESC')
                ->orderBy($OrderByColunm2[$courier_name], 'DESC')
                ->get()
                ->toArray();

            $columnName = $result[0]['forwarder_3_awb'] == '' ? 'last_mile_status' : 'first_mile_status';
            $courierActivities = StatusManagement::select('courier_status')
                ->join('courier', 'courier.id', '=', 'status_master.courier_id')
                ->where($columnName, 1)
                ->where('courier_name', $courier_name)
                ->get()
                ->toArray();
            $courierStatus = [];
            foreach ($courierActivities as $courierActivity) {
                $courierStatus[] = $courierActivity['courier_status'];
            }

            foreach ($forwarder2_data as $data) {
                foreach ($data as $res) {
                    if (in_array(strtoupper($res), $courierStatus)) {

                        $forwarder2_record[] = ['courier_name' => $courier_name, ...$data];
                    }
                }
            }
        }
        $forwarder3_record = [];
        if ($result[0]['forwarder_3_awb'] != '' && $result[0]['forwarder_3_flag'] == 0) {

            $awb_no = $result[0]['forwarder_3_awb'];
            $courier_name = $result[0]['courier_partner3']['courier_names']['courier_name'];
            $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

            $forwarder3_data = $table->select($selectColumns[$courier_name])
                ->where('awbno', $awb_no)
                ->orderBy($OrderByColunm1[$courier_name], 'DESC')
                ->orderBy($OrderByColunm2[$courier_name], 'DESC')
                ->get()
                ->toArray();

            $columnName = $result[0]['forwarder_4_awb'] == '' ? 'last_mile_status' : 'first_mile_status';
            $courierActivities = StatusManagement::select('courier_status')
                ->join('courier', 'courier.id', '=', 'status_master.courier_id')
                ->where($columnName, 1)
                ->where('courier_name', $courier_name)
                ->get()
                ->toArray();

            $courierStatus = [];
            foreach ($courierActivities as $courierActivity) {
                $courierStatus[] = $courierActivity['courier_status'];
            }

            foreach ($forwarder3_data as $data) {
                foreach ($data as $res) {
                    if (in_array(strtoupper($res), $courierStatus)) {

                        $forwarder3_record[] = ['courier_name' => $courier_name, ...$data];
                    }
                }
            }
        }

        $forwarder4_record = [];
        if ($result[0]['forwarder_4_awb'] != '' && $result[0]['forwarder_4_flag'] == 0) {

            $awb_no = $result[0]['forwarder_4_awb'];
            $courier_name = $result[0]['courier_partner4']['courier_names']['courier_name'];
            $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

            $forwarder4_data = $table->select($selectColumns[$courier_name])
                ->where('awbno', $awb_no)
                ->orderBy($OrderByColunm1[$courier_name], 'DESC')
                ->orderBy($OrderByColunm2[$courier_name], 'DESC')
                ->get()
                ->toArray();

            $courierActivities = StatusManagement::select('courier_status')
                ->join('courier', 'courier.id', '=', 'status_master.courier_id')
                ->where('last_mile_status', 1)
                ->where('courier_name', $courier_name)
                ->get()
                ->toArray();

            $courierStatus = [];
            foreach ($courierActivities as $courierActivity) {
                $courierStatus[] = $courierActivity['courier_status'];
            }

            foreach ($forwarder4_data as $data) {
                foreach ($data as $res) {
                    if (in_array(strtoupper($res), $courierStatus)) {

                        $forwarder4_record[] = ['courier_name' => $courier_name, ...$data];
                    }
                }
            }
        }
        // $records = [...$forwarder1_record, ...$forwarder2_record, ...$forwarder3_record, ...$forwarder4_record];
        $records = [...$forwarder4_record, ...$forwarder3_record, ...$forwarder2_record, ...$forwarder1_record];
        return view('shipntrack.Smsa.packetDetails', compact('forwarder_details', 'records'));
    }

    public function getDetails()
    {
        commandExecFunc('mosh:courier-tracking');
        return Redirect::back()->with('success', 'Fetching details please wait..');
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
